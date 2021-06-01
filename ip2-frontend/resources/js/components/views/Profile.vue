<template>
    <div>
        <a-layout>
            <a-layout-header id="info" style="background:white;">
                <a-avatar style="float:left;" :size="86" icon="user" src="https://cdn.dribbble.com/users/986811/screenshots/4601947/argentina_leo_messi.png?compress=1&resize=400x300"/>
                <h1 id="username" style="float:left;">{{ user.firstName }} {{ user.lastName }} <span>(#{{ user.id }})</span></h1>
            </a-layout-header>
            <a-layout>
                <a-layout-content style="background: white;" class="content">
                    <h3>Your information</h3>
                    <a-descriptions bordered>
                        <a-descriptions-item  label="Email">
                            <p id="item">{{ user.email }}</p>
                        </a-descriptions-item>
                        <a-descriptions-item label="Birthday">
                            <p >{{ user.birthday }}</p>
                        </a-descriptions-item>
                        <a-descriptions-item label="Study">
                            <p>{{ user.study }}</p>
                        </a-descriptions-item>
                    </a-descriptions>
                
                <br> 
                <div>
                    <h2>My events</h2>
                         <div class="event" v-for="myEvent in myEvents" :key="myEvent.id">
                            <router-link :to="{name: 'event', params: { id: myEvent.id}}">
                                    <b-card class="event-card">
                                    <div class="card-heading absolute top-3 left-3">
                                        <h3 class="title"> {{ myEvent.name }} </h3>
                                    </div>

                                     <div class="absolute date">
                                        <div class="top-date absolute inset-x-0 top-0 h-12 bg-red-500">
                                            <span class="text-white font-bold text-2xl absolute top-2 left-6 right-6">{{ getDay(myEvent.startEvent) }}</span>
                                        </div>
                                        <div class="bottom-date absolute inset-x-0 bottom-0 top-12 left-6 right-5 h-8">
                                            <span class="text-black font-bold text-l ">{{ getMonth(myEvent.startEvent) }}</span>
                                        </div>
                                    </div>
                                </b-card>
                            </router-link>
                        </div>
                    </div>
                </a-layout-content>          
            </a-layout>
        </a-layout>
    </div>
    
</template>
<script>
export default {
    data() {
        return {
            user: {},

            myEvents: {}
        }
    },
    created(){
            this.axios
            .get(`${this.$api}/api/users/5`)
            .then((response) => {
                // handle success
                this.user = response.data;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });


            //Show events 
            this.axios
            .get(`${this.$api}/api/showByUser/5`)
            .then((response) => {
                // handle success
                this.myEvents = response.data;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });
    }, 
    methods: {
        getDay(date) {
            var date = date.substring(8,10);

            return date;
        },
        getMonth(date) {
            var date = date.toString().substring(5,7);

            switch(date) {
                case "01":
                    return "jan";
                    break;
                case "02":
                    return "feb";
                    break;
                case "03":
                    return "mar";
                    break;
                case "04":
                    return "apr";
                    break;    
                case "05":
                    return "may";
                    break;
                case "06":
                    return "jun";
                    break;
                case "07":
                    return "jul";
                    break;
                case "08":
                    return "aug";
                    break;  
                case "09":
                    return "sep";
                    break;
                case "10":
                    return "oct";
                    break;
                case "11":
                    return "nov";
                    break;
                case "12":
                    return "dec";
                    break;                      
            }
        }
    }
}
</script>

<style scoped>
.event {
    float: left;
}

.event-card {
    width: 345px;
    height: 135px;
    margin-right: 10px;
    margin-top: 20px;
    padding: 5px;
    color: black;
    border-radius: 8%;
}

.event-card:hover {
    background-color: rgb(241, 240, 240);
}

.date {
    width: 82px;
    height: 82px;
    border-radius: 15px;
    top: -15px;
    right: 10px;
    border: solid black 1px;
}

.top-date {
    border-radius: 15px 15px 0 0;
}

.bottom-date {
    border-radius: 0 0 15px 15px;
}

.description {
    height: 100px;
    width: 250px;
}

.title {
    width: 240px;
}
.content{
    padding:15px;
}

#info{
    height: 120px;
}
#username{
    padding:15px;
}

@media screen and (max-width: 480px) {
  .event-card {
    width: 220px;
    height: 200px;
  }
  .title{
    width: 110px;
  }
  #info{
    height: 260px;
  }
  #username{
      padding: 0px;
  }
  .content{
      padding: 0px;
  }
  #item{
      width: 40px;
  }
  
  
}

@media screen and (max-width: 300px) {
  .event-card {
    width: 160px;
    height: 260px;
    margin-top:30px
  }
  .title{
    margin-top: 60px;
    width: 110px;
  }
  #info{
    height: 260px;
  }
  #username{
      padding: 0px;
  }
  .content{
    padding: 0px;
  }
}
</style>
