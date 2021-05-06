<template>
    <div>

        <b-container class="mx-auto">
             <div class="d-flex justify-content-center" style="margin-top: 10%">
                 <b-form @submit.prevent="addEvent">


                    <!-- Name -->
                    <div v-if="showName">
                        <h1>Enter a name for your event</h1>
                        <b-form-group id="input-group-name">
                            <b-form-input
                                id="input-name"
                                v-model="event.name"
                                type="text"
                                placeholder="Enter name (max. 30 characters)"
                                required>
                            </b-form-input>
                        </b-form-group> 

                        <b-button @click="showDescForm()">Next</b-button>


                

                    </div>


                    <!-- Description -->
                    <transition name="slide-fade">
                        <div v-if="showDescription">
                            <h1>Describe your event</h1>

                            <vue-editor v-model="event.description"></vue-editor>
                            
                            <br>

                            <b-button @click="showDateForm()">Next</b-button>
                        </div>
                    </transition>

                    <!-- Date/Time -->
                    <transition name="slide-fade">
                        <div v-if="showDate">
                            <h1>What type of event is it?</h1>
                            <b-form-group>
                                <b-form-radio-group plain>
                                    <b-form-radio v-model="x" name="oneday-radios" value="one">One day event</b-form-radio>
                                    <b-form-radio v-model="x" name="multdays-radios" value="mult">Multiple days event</b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                        </div>
                    </transition>

                    <br><br>


                    <transition name="slide-fade">
                        <div v-if="x === 'one'">
                            <h3>Start at</h3>
                            <a-date-picker />    
                            <a-time-picker format="h:mm"/>

                            <br><br>

                            <h3>Ends at</h3>
                            <a-date-picker />    
                            <a-time-picker format="h:mm"/>
                            <b-button @click="showLocationForm()">Next</b-button>
                        </div>
                    </transition>

                    <transition name="slide-fade">
                        <div v-if="x === 'mult'">
                            <h3>Start at</h3>
                            <a-date-picker @change="onStartsAtDate"/>        
                            <a-time-picker @change="onStartsAtTime" format="h:mm"/>

                            <br><br>

                            <h3>Ends at</h3>
                            <a-date-picker @change="onEndsAtDate" />    
                            <a-time-picker @change="onEndsAtTime" format="h:mm"/>
                            <b-button @click="showLocationForm()">Next</b-button>
                        </div> 
                    </transition>

                    <!-- Location -->
                    <transition name="slide-fade">
                        <div v-if="showLocation">
                            <h1>Where does the event happen?</h1>
                            <b-form-group id="input-group-loc">
                                <b-form-input
                                    id="input-location"
                                    v-model="event.location"
                                    type="text"
                                    placeholder="Enter a location"
                                    required>
                                </b-form-input>
                            </b-form-group>


                            <b-button @click="showEndForm()">Next</b-button>
                        </div>
                    </transition>    

                    <!-- Summary -->
                    <transition name="slide-fade">
                        <div v-if="showEnd">
                            <h1>Is this information correct?</h1>
                            <h3> {{ event.name }} </h3>
                            <p style="width:200px;" v-html="event.description"></p>
                            <p> {{ event.startsAtDate }} at {{ event.startsAtTime }} </p>
                            <p> {{ event.endsAtDate }} at {{ event.endsAtTime }} </p>
                            <p> {{ event.location }} </p>


                            <b-button type="submit" variant="primary">Create event</b-button>
                        </div>
                    </transition>  
                 </b-form>
             </div>
        </b-container>
    </div>
</template>
<script>
export default {
    data() {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        const minDate = new Date(today);
        minDate.setYear(minDate.getFullYear());
        minDate.setMonth(minDate.getMonth());
        minDate.setDate(minDate.getDate() + 1);

        return {
            event: {},
            showName: true,
            showDescription: false,
            showLocation: false,
            showEnd: false,

            showDate: false,
            showDateOneDay: false,
            showDateMultDay: false,

            min: minDate,
            state:false,

            x:''
        }
    },
    methods: {
        addEvent() {
            this.axios
                .post('http://127.0.0.1:8000/api/events', this.event)
                .then(response => (
                    this.$router.push({ name: 'home' })
                ))
                .catch(err => console.log(err))
                .finally( () => this.loading = false)
        },
        showDescForm() {
            this.showName = false;

            this.showDescription = true;
        },
        showDateForm(){
            this.showDescription = false;

            this.showDate = true;
        },
        showLocationForm() {
            this.showDate = false;
            this.x =false;

            this.showLocation = true;
        },
        showEndForm() {
            this.showLocation = false;

            this.showEnd = true;
        },
        onStartsAtDate(date, dateString) {
            this.event.startsAtDate = dateString;
        },
        onStartsAtTime(time, timeString) {
            this.event.startsAtTime = timeString;
        },
        onEndsAtDate(date, dateString) {
            this.event.endsAtDate = dateString;
        },
        onEndsAtTime(time, timeString) {
            this.event.endsAtTime = timeString;
        }
    }
}
</script>
<style scoped>


.eventType:hover {
    background-color: rgb(238, 238, 238);
    cursor: pointer;
}

.slide-fade-enter-active {
  transition: all 0.8s ease;
}
.slide-fade-leave-active {
  transition: all 0.5s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
}
</style>
