<template>
    <div>
         <a-layout>
            <a-layout-header style="background:white; height:175px; padding: 10px;">
                
                <div style="display:block; width:80%">
                    <h1 class="absolute top-6 left-26" style=""> {{ event.name }} <span>(#{{ event.user_id }})</span></h1>
                </div>
                <div style="display:block; float:right; width:20%">
                    <b-button id="subOrUnSubButton" class="" variant="danger"  @click="subOrUnsub()">Participate</b-button>
                    <router-link :to="{name: 'edit', params: { id: event.id}}">
                        <b-button v-if="showEditButton" class="" variant="primary">Edit event</b-button>
                    </router-link>
                </div>
               
                <br>
                <div style="float:left;">
                    <b-card class="date">
                        <div class="top-date absolute inset-x-0 top-0 h-12 bg-red-500">
                            <span class="text-white font-bold text-2xl absolute top-2 left-6 right-6">{{ getDay(event.startEvent) }}</span>
                        </div>
                        <div class="bottom-date absolute inset-x-0 bottom-0 top-7 left-6 right-5 h-8">
                            <span class="text-black font-bold text-l ">{{ getMonth(event.startEvent) }}</span>
                        </div> 
                    </b-card>
                </div>
                <div style="float:left; padding:10px;">
                    <h1> {{ getTime(event.startEvent) }} - {{ getTime(event.endEvent) }}</h1>
                </div>

                 <div style="float:left;">
                    <b-card class="date">
                        <div class="top-date absolute inset-x-0 top-0 h-12 bg-red-500">
                            <span class="text-white font-bold text-2xl absolute top-2 left-6 right-6">{{ getDay(event.endEvent) }}</span>
                        </div>
                        <div class="bottom-date absolute inset-x-0 bottom-0 top-7 left-6 right-5 h-8">
                            <span class="text-black font-bold text-l ">{{ getMonth(event.endEvent) }}</span>
                        </div> 
                    </b-card>
                </div>
            </a-layout-header>

            <a-layout>
                <a-layout-content style="background: white; padding:10px; height:700px;">
                    <div class="">
                        <h2>About</h2>
                        <p> {{ event.description }} </p>
                    </div>
                </a-layout-content>

                <a-layout-sider style="background:white; padding:10px; height:700px;">
                    <div v-if="showAttendees">
                        <h2>Attendees</h2>
                        <ul  style="overflow:hidden; overflow-y:scroll; height:640px;">
                            <li  v-for="sub in subscribers" :key="sub.id" style="margin-bottom: 5px;">
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>{{ sub.firstName +' '+ sub.lastName }}</span>
                            </li>
                            <br>
                        </ul>
                    </div>
                </a-layout-sider>
            </a-layout>
        </a-layout>
        <pre>{{ subscribers.length }}</pre>
    </div> 
</template>
<script>
export default {
        data() {
            return {
                event: {},
                user: {},

                event_subscriber: {},
                subscribers: {},
                showAttendees: false,
                isSubscribed: false,
                showEditButton:false,
            }
        },
        async created() {
            //Fetch the event (by id)
            await this.axios
                .get(`http://localhost:8000/api/events/${this.$route.params.id}`)
                .then((res) => {
                    this.event = res.data;
                    this.event_subscriber.event_id = this.event.id;
                })
                .catch(function (error) {
                    console.log(error);
                }).then(()=> {
                    
                });

            //Fetch logged in user
            await this.axios
                .get(`http://127.0.0.1:8000/api/users/10`)
                .then((response) => {
                    this.user = response.data
                    this.event_subscriber.user_id = this.user.id;
                })
                .catch(function (error) {
                    console.log(error);
                });

            //Fetch all attendees
            this.refreshAttendees();

            //check person is subscribed to event
            this.checkIfSubscribed();
            //check if person is owner of the event
            this.checkIfOwnerEvent();           
        },
        methods: {
            deleteEvent(id) {
                this.axios
                    .delete(`http://127.0.0.1:8000/api/events/${this.$route.params.id}`)
                    .then(response => {
                        let i = this.event.map(data => data.id).indexOf(id);
                        this.event.splice(i, 1);
                    });
            },
            getDay(date) {
                var day = date.substring(8,10);

                return day;
            },
            getMonth(date) {
                var month = date.substring(5,7);
                

                switch(month) {
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
            },
            getTime(time){
                var result = time.substring(11,16);

                return result;
            },

            refreshAttendees(){
                axios
                .get(`http://127.0.0.1:8000/api/showSubscribers/${this.$route.params.id}`)
                .then((response) => {
                    this.subscribers = response.data
                    this.showAttendees = true;
                    console.log(this.subscribers)
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            subOrUnsub(){
                this.checkIfSubscribed()
                if(this.isSubscribed == 0)
                {
                    axios
                    .post(`http://127.0.0.1:8000/api/participate/`, this.event_subscriber)
                    .then((reponse) => {
                        console.log(reponse);
                        //document.getElementById("subOrUnSubButton").textContent="Participate";
                        this.refreshAttendees(); 
                    })
                    .catch(function (error){
                        console.log(error);
                    });

                }
                else
                {
                    axios
                    .post(`http://127.0.0.1:8000/api/unparticipate/`, this.event_subscriber)
                    .then((reponse) => {
                        console.log(reponse);
                        //document.getElementById("subOrUnSubButton").textContent="UnParticipate";
                        this.refreshAttendees();
                    })
                    .catch(function (error){
                        console.log(error);
                    });
                }
                this.checkIfSubscribed();
                
            }, 

            checkIfSubscribed(){
                axios
                .post(`http://127.0.0.1:8000/api/checkIfSubscribed/`, this.event_subscriber)
                .then((response) => {
                    this.isSubscribed = response.data;
                    console.log(this.isSubscribed);
                    var x = document.getElementById("subOrUnSubButton");
                    if(this.isSubscribed==0){
                        x.innerHTML="Participate";
                    }else{
                        x.innerHTML="UnParticipate";
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            checkIfOwnerEvent(){
                console.log(this.event.user_id);
                if(this.event.user_id == this.event_subscriber.user_id){
                    this.showEditButton=true;
                }
            }
            
            
        }
}
</script>
<style scoped>
.date {
    width: 80px;
    height: 80px;
    border-radius: 15px;
    border: solid 1px black;
}

.top-date {
    border-radius: 15px 15px 0 0;
}

.bottom-date {
    border-radius: 0 0 15px 15px;
}
</style>
