let amqp = require("amqplib/callback_api");
let os = require("os");
let fs = require("fs");
const fetch = require('node-fetch');



{
    (RABBITMQ = "amqp://10.3.56.6"),
    (QUEUE = "heartbeat");
}

let channel;
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

function SendHeartBeat() {
  fetch('http://127.0.0.1:8000/api/status').then(response => response.json()).then(data => {
    if(!data.error || !data){
      channel.sendToQueue(QUEUE, Buffer.from(data.msg))
      console.log(" [x] Sent %s", data.msg);
    }else{
      clearInterval();
    }
  });
}