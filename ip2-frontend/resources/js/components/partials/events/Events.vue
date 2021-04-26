<template>
        <div>
            <div class="event" v-for="event in events" :key="event.id">
                <router-link :to="{name: 'event', params: { id: event.id}}">
                    <b-card class="event-card">
                        <div class="card-heading absolute top-3 left-3">
                            <h3 class="title"> {{ event.name }} </h3>
                            <p class="description"> {{ event.description }} </p>
                        </div>

                        <div class="absolute top-3 right-3 date">
                            <span class="date-day">{{ getDay(event.startsAt) }}</span>
                            <span class="date-month">{{ getMonth(event.startsAt) }}</span>
                        </div>
                    </b-card>
                </router-link>
            </div>
        </div>
</template>
<script>
export default {
    data() { 
        return {
            events: {}
        }
    },
    created() {
            this.axios
            .get('http://127.0.0.1:8000/api/events')
            .then(response => {
                this.events = response.data;
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
    width: 350px;
    height: 200px;
    margin-right: 10px;
    margin-top: 10px;
    padding: 5px;
    color: black;
    border-radius: 8%;
}

.event-card:hover {
    background-color: rgb(241, 240, 240);
}

.date {
    display: flex;
    flex-direction: column;
    background-color: #ffdada;
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    padding:25px;
}

.date-day {
    color: #dd3030;
    font-weight: 500;
    font-size: 1.5rem;
    line-height: 1;
}

.date-month {
    color: #dd3030;
    line-height: 1;
    font-size: 1rem;
    text-transform: uppercase;
}

.description {
    height: 100px;
    width: 250px;
}

.title {
    width: 250px;
}
</style>