#!/bin/sh

CONFIG_DIR=/etc/logstash/conf.d
TEMPLATES_DIR=/etc/logstash/config-templates

echo "clean up old configuration files"
rm $CONFIG_DIR/*.conf

echo "load new configuration files"
cp $TEMPLATES_DIR/*.conf $CONFIG_DIR/

echo "sed: replacing configuration variables with .env parameters"
for conf_path in $CONFIG_DIR/*conf; do 
    echo "setting value for config in $conf_path"
    sed -ie "s/{{mysql_host}}/$MYSQL_HOST/g" $conf_path
    sed -ie "s/{{mysql_port}}/$MYSQL_PORT/g" $conf_path
    sed -ie "s/{{mysql_name}}/$MYSQL_NAME/g" $conf_path
    sed -ie "s/{{mysql_user}}/$MYSQL_USER/g" $conf_path
    sed -ie "s/{{mysql_pass}}/$MYSQL_PASS/g" $conf_path
    sed -ie "s/{{mongo_user}}/$MONGO_USER/g" $conf_path
    sed -ie "s/{{mongo_pass}}/$MONGO_PASS/g" $conf_path
    sed -ie "s/{{mongo_dbnm}}/$MONGO_NAME/g" $conf_path
    sed -ie "s/{{mongo_host}}/$MONGO_HOST/g" $conf_path
    sed -ie "s/{{mongo_port}}/$MONGO_PORT/g" $conf_path
done

echo "cleaning up after sed"
rm $CONFIG_DIR/*.confe

#todo: check if plugin exists first before redownloading

echo "installing plugins"
logstash-plugin install --version=3.1.5 logstash-output-mongodb 

#start logstash with configuration folder
echo "starting logstash with configuration dir: '$CONFIG_DIR'"
logstash -f $CONFIG_DIR
