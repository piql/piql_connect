#!/bin/sh

can_write_dir() {
  path=$1
  if [ ! -d $path ]; then
    echo "$path does not exist";
    return 1
  elif ! [[ -w "$path" ]] ; then
    echo "Cannot write to '$path'";
    return 1
  fi
  return 0
}

can_read_dir() {
  path=$1
  if [ ! -d $path ]; then
    echo "$path does not exist";
    return 1
  elif ! [[ -r "$path" ]] ; then
    echo "Cannot write to '$path'";
    return 1
  fi
  return 0
}

create_dir_if_not_exists() {
    path=$1
    if [ ! -d $path ]; then
        echo "creating directory $path";
        mkdir -p $path || return 1
    fi
    return 0
}

configure_ingest_templates() {
    template_dir=$1
    config_dir=$2
    
    if [[ "$(can_read_dir $template_dir)" -eq 1 ]]; then 
        return 1
    elif [[ "$(can_write_dir $config_dir)" -eq 1 ]]; then 
        return 1
    fi

    echo "clean up old configuration files";
    config_dir_files=($config_dir/*.conf)
    if [ ${#config_dir_files[@]} -gt 0 ]; then 
      rm $config_dir/*.conf
    fi

    echo "load new configuration files";
    cp $template_dir/*.conf $config_dir/

    echo "sed: replacing configuration variables with .env parameters";
    for conf_path in $config_dir/*conf; do 
        echo "processing $conf_path ...";
        sed -ie "s/{{mysql_host}}/$MYSQL_HOST/g" $conf_path;
        sed -ie "s/{{mysql_port}}/$MYSQL_PORT/g" $conf_path;
        sed -ie "s/{{mysql_name}}/$MYSQL_NAME/g" $conf_path;
        sed -ie "s/{{mysql_user}}/$MYSQL_USER/g" $conf_path;
        sed -ie "s/{{mysql_pass}}/$MYSQL_PASS/g" $conf_path;
        sed -ie "s/{{mongo_user}}/$MONGO_USER/g" $conf_path;
        sed -ie "s/{{mongo_pass}}/$MONGO_PASS/g" $conf_path;
        sed -ie "s/{{mongo_dbnm}}/$MONGO_NAME/g" $conf_path;
        sed -ie "s/{{mongo_host}}/$MONGO_HOST/g" $conf_path;
        sed -ie "s/{{mongo_port}}/$MONGO_PORT/g" $conf_path;
    done

    echo "cleaning up after sed..";
    rm $config_dir/*.confe
}


# do stuff
CONFIG_DIR=/tmp/logstash/conf.d
TEMPLATES_DIR=/etc/logstash/config-templates
CONFIG_PATH=/usr/share/logstash/config/pipelines.yml

create_dir_if_not_exists $CONFIG_DIR

echo "";
echo "processing pipelined configurations";
echo "";
plugins=("inputs" "outputs")
for i in "${plugins[@]}"; do
    cnf_dir="$CONFIG_DIR/$i"
    tmp_dir="$TEMPLATES_DIR/$i"
    
    echo "";
    echo "processing pipelined $i configurations";
    if [[ "$(create_dir_if_not_exists $cnf_dir)" -eq 1 ]]; then 
        exit 1
    elif [[ "$(configure_ingest_templates $tmp_dir $cnf_dir)" -eq 1 ]]; then 
        exit 1
    fi
    echo "completed processing pipelined $i configurations";
done

#todo: check if plugin exists first before redownloading

echo "installing modified mongodb plugin (with upsert)";
logstash-plugin install /etc/logstash/logstash-output-mongodb-3.2.0.gem

#start logstash with configuration folder
echo "starting logstash with pipeline config: '$CONFIG_PATH'";
logstash 
