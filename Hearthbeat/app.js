let amqp = require("amqplib/callback_api");
let os = require("os");
let fs = require("fs");
const fetch = require('node-fetch');

const sleep = (milliseconds) => {
  return new Promise(resolve => setTimeout(resolve, milliseconds))
}



{
    (RABBITMQ = "amqp://10.3.56.6"),
    (QUEUE = "heartbeat");
}

let channel;
let error = null;

code();
async function code() {
  do {
     try {
      amqp.connect(RABBITMQ, function (error0, connection) {
        console.log("Connected to the rabbitMQ");
        if (error0) {
          throw error0;
        }
        connection.createChannel(function (error1, getChannel) {
          if (error1) {
            throw error1;
          }
          channel = getChannel;
      
          setInterval(SendHeartBeat, 1000);
        });
      });
    } catch (errors) {
      error = errors;
      
    }
    console.log("RabbitMQ is down");
    await sleep(3000);
  } while (error);
}

async function SendHeartBeat() {
  try {
    await fetch('http://localhost:80/api/status').then(response => response.json()).then(data => {
    if(!data.error || !data){
      channel.sendToQueue(QUEUE, Buffer.from(data.msg))
      console.log(" [x] Sent %s", data.msg);
    }else{
      clearInterval();
    }
  });
  } catch (errors) {
    console.log("Service is down");
    clearInterval();
  }
  
}