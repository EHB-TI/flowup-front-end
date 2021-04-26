<template>
    <div class=" d-flex flex-row ">
        <div class="d-flex flex-column mt-3 greybg">
            <div class=" d-flex flex-row">
                <h4>19:20 - 20:30</h4> 
                <div class="date d-flex flex-column date mx-6">
                <div>   
                        <span class="date-day">{{ getDay(event.startsAt)}}</span>
                </div>

                <div> 
                        <span class="date-month">{{ getMonth(event.startsAt) }}</span>
                </div>
                
                </div>

            <h1 align-h="center" class="align-self-center">{{event.name}}</h1>
        </div>
            

            <div class="d-flex flex-column mt-5">
                <h3>About</h3>
                <p>{{event.description}}</p>
            
            </div>
        </div>
        <div class="mt-5 d-flex flex-column align-items-center">
            <h3>Attendees</h3>
            <b-list-group>
                <b-list-group-item>Bob de bauwer</b-list-group-item>
                <b-list-group-item>Bob de bauwer</b-list-group-item>
                <b-list-group-item>Bob de bauwer</b-list-group-item>
            </b-list-group>
        </div>        
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
                        this.$router.push({ name: 'home' });
                    });
            },getDay(date) {
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
.greybg{
    background-color: #F3F5F2;
    height: 100vh;
    width: 80%;
    padding: 20px;
}

.greybg + div{
    width: 20%;
    background-color:white;
}

h1{
    text-align: center;
    margin-left: 25%;
}

.date {
    border-radius: 10px;    
    border: 1px solid black;
    z-index: 3;
    width: 50px;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
    margin-left: 5%;
}

.date div{
    border-radius: 10px 10px 0 0;
    background-color: red;
}

.date div + div{
    border-radius: 0 0 10px 10px;
    background-color: white;
}

</style>
