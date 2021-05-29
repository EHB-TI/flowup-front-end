FROM node:14

WORKDIR /usr/src/app

COPY package*.json ./

# Installing JDK 8, deze jdk moet geïnstalleerd worden om sommige node packages te laten werken
RUN apt-get update && apt-get install -y openjdk-8-jdk

# Installing node modules
RUN npm install

COPY . .

CMD [ "node", "app.js" ]