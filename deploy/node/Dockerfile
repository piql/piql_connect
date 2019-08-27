FROM node:10.16.3

RUN chown -R 1000:1000 /usr/local/lib
COPY ./node/npm.sh /usr/local/bin/npm.sh
CMD ["npm.sh"]
