<template>
        <a-layout>
                <a-layout-content style="background: white; padding: 15px;">
                <div>
                    <h2>My participations</h2>
                         <div class="event" v-for="event in events" :key="event.event_id">
                            <router-link :to="{name: 'event', params: { id: event.event_id}}">
                                <b-card class="event-card">
                                    <div class="card-heading">
                                        <h3 class="title"> {{ event.name }} </h3>
                                    </div>

                                     <div class="absolute date">
                                        <div class="top-date absolute inset-x-0 top-0 h-12 bg-red-500">
                                            <span class="text-white font-bold text-2xl absolute top-2 left-6 right-6">{{ getDay(event.startEvent) }}</span>
                                        </div>
                                        <div class="bottom-date absolute inset-x-0 bottom-0 top-12 left-6 right-5 h-8">
                                            <span class="text-black font-bold text-l ">{{ getMonth(event.startEvent) }}</span>
                                        </div>
                                    </div>
                                </b-card>
                            </router-link>
                        </div>
                    </div>
                </a-layout-content>          
            </a-layout>
</template>
<script>
export default {
        data() { 
            return {
                
                user: {},

                //id, user_id, event_id  from table (event_subscribers)
                events: {},
            }
        },
        async created() {
            //fetch logged in user
            await this.axios
                .get(`http://127.0.0.1:8000/api/users/1`)
                .then((response) => {
                    this.user = response.data
                    console.log(this.user); 
                    console.log(this.user.id);             
                })
                .catch(function (error) {
                    console.log(error);
                });
            //fetch events logged in user attends
            this.axios
                .get(`http://127.0.0.1:8000/api/showEventsYouAttend/${this.user.id}`)
                .then((response) => {
                    this.events = response.data
                    console.log(this.events);
                   
                })
                .catch(function (error) {
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
    width: 80px;
    height: 80px;
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
    width: 250px;
}
</style>