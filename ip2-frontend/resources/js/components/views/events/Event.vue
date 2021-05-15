<template>
    <div>
         <a-layout>
            <a-layout-header style="background:white; height:175px; padding: 10px;">
                
                <div>
                    <h1 class="absolute top-6 left-26" style=""> {{ event.name }} </h1>
                    <b-button class="absolute top-10 right-16" variant="danger">Participate</b-button>
                     <router-link :to="{name: 'edit', params: { id: event.id}}">
                    <b-button class="absolute top-10 right-48" variant="primary">Edit event</b-button>
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
            </a-layout-header>

            <a-layout>
                <a-layout-content style="background: white; padding:10px; height:700px;">
                    <div class="">
                        <h2>About</h2>
                        <p v-html="event.description"></p>
                    </div>
                </a-layout-content>

                <a-layout-sider style="background:white; padding:10px; height:700px;">
                    <div>
                        <h2>Attendees</h2>
                        <ul style="overflow:hidden; overflow-y:scroll; height:640px;">
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                            <li>
                                <a-avatar shape="circle" size="large" icon="user" />
                                <span>Mark Zuckerberg</span>
                            </li>
                            <br>
                        </ul>
                    </div>
                </a-layout-sider>
            </a-layout>
        </a-layout>
    </div> 
</template>
<script>
export default {
        data() {
            return {
                event: {}
            }
        },
        created() {
            this.axios
                .get(`http://localhost:8000/api/events/${this.$route.params.id}`)
                .then((res) => {
                    this.event = res.data;
                    console.log(this.event);
                });
                
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

                console.log(day);
                console.log(date);

                return day;
            },
            getMonth(date) {
                var month = date.substring(5,7);
                console.log(month);

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
                console.log(result);
                return result;
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
