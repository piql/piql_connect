import docker

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
        print('PiqlConnect Docker containers are not running')
        return False
    else:
        container_state = container.attrs['State']
        container_is_running = container_state['Status'] == 'running'
        return container_is_running

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
        result = active_container.exec_run(command, stdout=True, stderr=True, stdin=False, tty=False, 
                                            privileged=False, user=user, detach=False, stream=False, 
                                            socket=False, environment=None, workdir=None, demux=False)
        if (result[0] != 0):
            print('The command failed.')
            print(result)
            
        else:
            print('The command was run successfully.')
    else:
        print('The requested container is not running. Terminating.')

run_container_command('piqlconnect_mongodb_1', 
                    'mongo -u connectuser -p Fw86TQZrJ5 --authenticationDatabase admin connectstats -eval "printjson(db.dropDatabase())"', 
                    'root')
