<template>
    <div>
        <b-row>
            <b-col class="" cols="8">
                <div>
                    <div class="">
                        <h1> {{ event.name }} </h1>
                    </div>
                </div>

                <div class="relative top-10">
                    <div>
                        <b-card style="float:left;" class="date">
                            <div class="top-date absolute inset-x-0 top-0 h-12 bg-red-500">
                                <span class="text-white font-bold text-2xl absolute top-2 left-6 right-6">{{ getDay(event.startsAtDate) }}</span>
                            </div>
                            <div class="bottom-date absolute inset-x-0 bottom-0 top-12 left-6 right-5 h-8">
                                <span class="text-black font-bold text-l ">{{ getMonth(event.startsAt) }}</span>
                            </div>
                        </b-card>
                    </div>

                    <div style="float:left;">
                        <h1 style="margin-top:15px; margin-left:25px;"> {{ event.startsAt }} - {{ event.endsAt }} </h1>
                    </div> 
                </div>
                <br>              
                <br>
                <br>
                <br>
                <div style="float:left;" class="relative top-32">
                    <h2 class="">Description</h2>
                    <p style="overflow:hidden; overflow-y:scroll; height:575px; width:950px;">Lorem Ipsum is simply dummy Lorem Ipsum is simply dummy Lorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is Lorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummyLorem Ipsum is simply dummy simply dummyLorem Ipsum is simply dummy</p>
                </div>
            </b-col>


            <b-col class="top-32 text-center"  cols="4">
                <div>
                    <div>
                        <h2>Attendees</h2>
                        <ul style="overflow:hidden; overflow-y:scroll; height:200px;">
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                             <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                             <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                            <li>Mark Zuckerberg</li>
                        </ul>
                    </div>
                </div>    
            </b-col>
        </b-row>
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

                return day;
            },
            getMonth(date) {
                var month = date.toString().substring(5,7);

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
