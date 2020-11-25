import os
import config
import docker
import subprocess
from shutil import copyfile

DOCKER_CLIENT = docker.DockerClient(base_url='unix://var/run/docker.sock')

def docker_running(container_name):
    """
    Verify the running state of a container by its name.
    :param (str)container_name: The name of the container.
    :return: Bool if the container is running.
    """
    
    try:
        container = DOCKER_CLIENT.containers.get(container_name)
    except docker.errors.NotFound:
        print('PiqlConnect Docker containers are not running - Continuing.')
        return False
    else:
        container_state = container.attrs['State']
        container_is_running = container_state['Status'] == 'running'
        return container_is_running

def docker_create_volume(volume_name):
    """
    Create a new Docker volume.
    :param (str)volume_name: The name of the Docker volume.
    :return: No return value.
    """
    try:
        DOCKER_CLIENT.volumes.get(volume_name)
    except docker.errors.NotFound:
        print('Creating Docker volume ' + volume_name)
        try:
            DOCKER_CLIENT.volumes.create(name=volume_name, driver='local')
        except docker.errors.APIError:
            print('The Docker volume ' + volume_name + ' was not created. Terminating.')
            exit()
        else:
            print('The Docker volume ' + volume_name + ' has been created.')
    except docker.errors.APIError:
        print('Could not get the Docker volumes. Terminating.')
        exit()
    else:
        print('The Docker volume ' + volume_name + ' already exists. Skipping.')

def docker_network_connect(network_name, containers):
    """
    Create a link between 2 containers over a Docker network.
    :param (str)network_name: The name of the Docker network.
    :param (list)containers: The Docker network driver type.
    :return: No return value.
    """
    try:
        DOCKER_CLIENT.networks.get(network_name)
    except docker.errors.NotFound:
        print('The network ' + network_name + ' does not exist. Creating.')
        try:
            DOCKER_CLIENT.networks.create(name = network_name, driver = 'Bridge')
        except docker.errors.APIError:
            print('The network was not created. Terminating.')
            exit()
    except docker.errors.APIError:
        print('Unable to get the Docker networks. Terminating.')
        exit()

    first_container = containers[0]
    other_container = containers[1]

    try:
        process = subprocess.Popen(['docker', 'network', 'connect', '--link', first_container + ':' + config.hostname, 
                                    network_name, other_container],
                                    stdout=subprocess.PIPE,
                                    universal_newlines = True)
    
        while True:
            return_code = process.poll()
            if (return_code is not None):
                if (return_code != 0):
                    print('Unable to link networks. Terminating.')
                    exit()
                else:
                    print('Network link completed, continuing.')
                    break
    except:
        print('Network link exists. Continuing.')

def run_container_command(container_name, command, user):
    """
    Runs a command within a Docker container.
    :param (str)container_name: The name of the Docker container.
    :param (str)command: The command to be executed.
    :param (str)user: The user that will execute the command.
    :return: No return value.
    """
    if (docker_running(container_name) == True):
        active_container = DOCKER_CLIENT.containers.get(container_name)
        result = active_container.exec_run(command, stdout=False, stderr=True, stdin=False, tty=False, 
                                            privileged=False, user=user, detach=False, stream=False, 
                                            socket=False, environment=None, workdir=None, demux=False)
        if (result[0] != 0):
            print('The command failed. Terminating.')
            exit()
        else:
            print('The command was run successfully. Continuing.')
    else:
        print('The requested container is not running. Terminating.')
        exit()
        
if (config.config_OK == False):
    print("You need to set your configuration in the config.py file.")
    print("Build terminated.")
    exit()

if (config.update_AM_service_callbacks == True):
    if (os.path.isfile('/usr/bin/lynx')) == False:
        print("Installing Lynx. This is required for updating callbacks in Archivematica.")
        os.system('sudo apt update && sudo apt install -y lynx')

if (docker_running('piqlconnect_app_1')):
    print('PiqlConnect seems to be running. Shutting down, please wait...')
    process = subprocess.Popen(['docker-compose', '-p piqlconnect', 'down'],
                                stdout=subprocess.PIPE,
                                universal_newlines = True)
    
    while True:
        return_code = process.poll()
        if (return_code is not None):
            if (return_code != 0):
                print('Failed to terminate PiqlConnect. Please terminate manually and run the script again.')
                exit()
            else:
                print('PiqlConnect has been shut down, continuing.')
                break

if (os.path.isfile(config.piqlconnect_root_dir + '.env') == True):
    print('Backing up corrent configuration')
    os.rename(config.piqlconnect_root_dir + '.env', config.piqlconnect_root_dir + '.env.backup')
else:
    print('Configuration not found - Skipping backup.')

print('Creating configuration.')
envfile = open(config.piqlconnect_root_dir + '.env.example', "rt")
envfile_data = envfile.read()
if (config.dev_environment == True):
    envfile_data = envfile_data.replace('_APP_URL_', 'http://' + config.hostname)
else:
    envfile_data = envfile_data.replace('_APP_URL_', 'https://' + config.hostname)
envfile_data = envfile_data.replace('_STORAGE_LOCATION_ID_', config.storage_location_ID)
envfile_data = envfile_data.replace('_CONNECT_DB_USER_', config.connect_db_username)
envfile_data = envfile_data.replace('_CONNECT_DB_PASSWORD_', config.connect_db_password)
envfile_data = envfile_data.replace('_MONGO_DB_USERNAME_', config.mongo_db_username)
envfile_data = envfile_data.replace('_MONGO_DB_PASSWORD_', config.mongo_db_password)
envfile_data = envfile_data.replace('_S3_ACCESS_KEY_', config.S3_access_key)
envfile_data = envfile_data.replace('_S3_SECRET_KEY_', config.S3_secret_key)
envfile_data = envfile_data.replace('_S3_REGION_', config.S3_region)
envfile_data = envfile_data.replace('_S3_BUCKET_NAME_', config.S3_bucket_name)
envfile_data = envfile_data.replace('_S3_URL_', config.S3_URL)
envfile_data = envfile_data.replace('_KEYCLOAK_REALM_PUBLIC_KEY_', config.keycloak_realm_public_key)
envfile_data = envfile_data.replace('_KEYCLOAK_REALM_NAME_', config.keycloak_realm_name)
envfile_data = envfile_data.replace('_KEYCLOAK_CALLBACK_CLIENT_SECRET_', config.keycloak_callback_client_secret)
envfile_data = envfile_data.replace('_APP_AUTH_SERVICE_USERNAME_', config.keycloak_service_username)
envfile_data = envfile_data.replace('_APP_AUTH_SERVICE_PASSWORD_', config.keycloak_service_password)
envfile.close()
envfile = open(config.piqlconnect_root_dir + '.env', "wt")
envfile.write(envfile_data)
envfile.close()

print('Generating the environment.js file')
if (os.path.isfile('init-auth-client.sh')):
    process = subprocess.Popen(['./init-auth-client.sh', '-r' + config.keycloak_realm_name])
    process.wait()

print('Running Composer install')
if (os.path.isfile(config.piqlconnect_root_dir + 'composer.json')):
    process = subprocess.Popen(['composer', 'install'],
                                cwd = config.piqlconnect_root_dir,
                                stdout=subprocess.PIPE,
                                universal_newlines = True)
    
    while True:
        return_code = process.poll()
        if (return_code is not None):
            if (return_code != 0):
                print('Failed to run composer install. Please correct the error and try again.')
                exit()
            else:
                print('Composer install completed, continuing.')
                break
else:
    print('The composer.json file is missing from ' + config.piqlconnect_root_dir + '. Terminating.')
    exit()

print('Running NPM install')
if (os.path.isfile(config.piqlconnect_root_dir + 'package.json')):
    process = subprocess.Popen(['npm', 'install'],
                                cwd = config.piqlconnect_root_dir,
                                stdout=subprocess.PIPE,
                                universal_newlines = True)
    
    while True:
        return_code = process.poll()
        if (return_code is not None):
            if (return_code != 0):
                print('Failed to run NPM install. Please correct the error and try again.')
                exit()
            else:
                print('NPM install completed, continuing.')
                break
else:
    print('The package.json file is missing from ' + config.piqlconnect_root_dir + '. Terminating.')
    exit()

print('Installing Vue')
process = subprocess.Popen(['php', 'artisan', 'vue-i18n:generate'],
                            cwd = config.piqlconnect_root_dir,
                            stdout=subprocess.PIPE,
                            universal_newlines = True)
    
while True:
    return_code = process.poll()
    if (return_code is not None):
        if (return_code != 0):
            print('Failed to install Vue. Please correct the error and try again.')
            exit()
        else:
            print('Vue installed, continuing.')
            break

if (config.dev_environment == True):
    print('Running NPM dev')
    process = subprocess.Popen(['npm', 'run', 'dev'],
                                cwd = config.piqlconnect_root_dir,
                                stdout=subprocess.PIPE,
                                universal_newlines = True)
else:
    print('Running NPM prod')
    process = subprocess.Popen(['npm', 'run', 'prod'],
                                cwd = config.piqlconnect_root_dir,
                                stdout=subprocess.PIPE,
                                universal_newlines = True)
    
while True:
    return_code = process.poll()
    if (return_code is not None):
        if (return_code != 0):
            print('Failed to run NPM. Please correct the error and try again.')
            exit()
        else:
            print('NPM run completed, continuing.')
            break

print('Creating persistent storage for Docker containers')
docker_create_volume('am-pipeline-data')
docker_create_volume('ss-location-data')
docker_create_volume('piqlConnect-mariadb')
docker_create_volume('piqlConnect-mongodb')

print('Creating Docker network links.')
linked_containers = ['piqlconnect_nginx_1', 'compose_archivematica-storage-service_1']
docker_network_connect('piqlconnect_piqlConnect-net', linked_containers)

print('Starting Docker containers')
if (os.path.exists(config.piqlconnect_root_dir + 'laravel-echo-server.lock')):
    os.remove(config.piqlconnect_root_dir + 'laravel-echo-server.lock')

if (config.dev_environment == True):
    echo_template = open(config.piqlconnect_root_dir + 'laravel-echo-server.json.example', "rt")
    echo_config_data = echo_template.read()
    echo_template.close()
    echo_config_data = echo_config_data.replace('http', 'https')
    echo_config_file = open(config.piqlconnect_root_dir + 'laravel-echo-server.json', "wt")
    echo_config_file.write(echo_config_data)
    echo_config_file.close()
    copyfile('./.env.dev', './.env')
else:
    copyfile('./.env.dev', './.env')
    copyfile(config.piqlconnect_root_dir + 'laravel-echo-server.json.example', config.piqlconnect_root_dir + 'laravel-echo-server.json')

process = subprocess.Popen(['docker-compose', '-p piqlconnect', 'up', '--force-recreate', '-d'],
                            stdout=subprocess.PIPE,
                            universal_newlines = True)
while True:
    return_code = process.poll()
    if (return_code is not None):
        if (return_code != 0):
            print('Failed to start containers. Please correct the error and try again.')
            exit()
        else:
            print('Docker containers started, continuing.')
            break

print('Generating Laravel application key')
process = subprocess.Popen(['php', 'artisan', 'key:generate'],
                            cwd = config.piqlconnect_root_dir,
                            stdout=subprocess.PIPE,
                            universal_newlines = True)
    
while True:
    return_code = process.poll()
    if (return_code is not None):
        if (return_code != 0):
            print('Failed to generate key. Terminating.')
            exit()
        else:
            print('Application key generated, continuing.')
            break

print('Migrating databases.')
if (config.complete_database_wipe == True):
    run_container_command('piqlconnect_app_1', 'php artisan migrate:fresh', 'www-data')
else:
    run_container_command('piqlconnect_app_1', 'php artisan migrate', 'www-data')
print('Databases migrated. Continuing.')

print('Setting passport keys')
run_container_command('piqlconnect_app_1', 'php artisan passport:keys --force', 'www-data')

if (config.complete_database_wipe == True):
    print('Seeding database with default values.')
    run_container_command('piqlconnect_app_1', 'php artisan db:seed --class=' + config.database_seeder, 'www-data')
    print('Database seeded. Continuing.')

if (config.update_AM_service_callbacks == True):
    print('Updating Archivematica service callbacks.')
    os.system('./update-service-callbacks.php')

print('Piql Connect build has completed.')