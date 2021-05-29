let amqp = require("amqplib/callback_api");
let os = require("os");
let fs = require("fs");
const fetch = require('node-fetch');



{
    (RABBITMQ = "amqp://10.3.56.6"),
    (QUEUE = "heartbeat");
}

let channel;
let error = null;
do {
  error = setTimeout(rabbitmqCon, 3000);
  console.log("RabbitMQ is down");
} while (error);

function rabbitmqCon() {
  let error = null;
  try {
    amqp.connect(RABBITMQ, function (error0, connection) {
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
  return error
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
  } catch (error) {
    console.log("Service is down");
    clearInterval();
  }
  
}