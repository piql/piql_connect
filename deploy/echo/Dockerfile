FROM node:13-alpine

RUN apk add --update --no-cache bash \
	&& apk add --update --no-cache -t .build-deps python make g++ gcc \
	&& npm i -g laravel-echo-server \
	&& apk del .build-deps && npm install n -g

WORKDIR /app

ENTRYPOINT ["laravel-echo-server"]
CMD ["start"]
