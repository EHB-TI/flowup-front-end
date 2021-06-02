<template>

 <a-layout style="background:white;">
     <a-layout-header style="display:none; bg-red-500" id="header"><div id="message"></div></a-layout-header>
      <a-layout-content>

        <div>
            <div class="event static" v-for="event in events.data" :key="event.id">
                <router-link :to="{name: 'event', params: { id: event.id}}">
                    <b-card class="event-card ">
                        <div class="card-heading absolute top-3 left-3">
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
      <a-layout-footer style="background:white; height:65px;" class="mx-auto">
            <pagination :data="events" @pagination-change-page="getResults"></pagination>
      </a-layout-footer>
    </a-layout>

</template>
<script>
export default {
    data() {
        return {
            events: {},

            //api: process.env.MIX_API_CONN,


        }
    },

    created() {
        try{
            this.axios
            .get(`${this.$api}/api/events`)
            .then(response => {
                this.events = response.data;
            });
        }catch(err){
            let header = document.getElementById("header");
            let message = document.getElementById("message");
            message.innerHTML = err;
            header.style.display = block;
        }

    },
    methods: {
         getResults(page = 1) {
			axios.get(`${this.$api}/api/events?page=` + page)
				.then(response => {
					this.events = response.data;
				});
		},
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
</style>
