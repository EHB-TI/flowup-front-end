<template>
    <div>
        <div>
            <h1>Edit an event</h1>
        </div>

        <div>
            <b-form @submit.prevent="editEvent">
                <h3>Name of the event</h3>
                <b-form-group
                    id="input-group-1">

                    <b-form-input
                    id="input-name"
                    v-model="event.name"
                    type="text"
                    placeholder="Enter name (max. 30 characters)"
                    required>
                    </b-form-input>
                </b-form-group>


                <h3>Description for the event</h3>
                <b-form-group
                    id="input-group-2">

                    <b-form-textarea
                    id="input-description"
                    v-model="event.description"
                    type="text"
                    placeholder="Enter description (max. 50 characters)"
                    rows="3"
                    max-rows="6"
                    required>
                    </b-form-textarea>
                </b-form-group>
                
                <!-- Date/Time -->
                    <transition name="slide-fade">
                        <div v-if="showDate">
                            <h3>What type of event is it?</h3>
                            <a-alert
                                v-if="errorDate"
                                message="The date fields are all required."
                                type="error"
                                banner
                                style="margin-bottom:8px;" />
                            <b-form-group>
                                <b-form-radio-group plain>
                                    <b-form-radio v-model="x" name="oneday-radios" value="one">One day event</b-form-radio>
                                    <b-form-radio v-model="x" name="multdays-radios" value="mult">Multiple days event</b-form-radio>
                                </b-form-radio-group>
                            </b-form-group>
                        </div>
                    </transition>

                    <br>

                    <transition name="slide-fade">
                        <div v-if="x === 'one'">
                            <h4>Starts at</h4>
                            <a-date-picker
                                @change="onChangeOne"
                                format="YYYY-MM-DD HH:mm:ss"
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"/>

                            <h4>Ends at</h4>
                                <a-time-picker 
                                format="h:mm:ss" 
                                @change="onChangeOneTime"/>

                            <b-button @click="checkDate()">Next</b-button> 
                        </div>
                    </transition>

                    <transition name="slide-fade">
                        <div v-if="x === 'mult'">
                            <h4>Starts at</h4>
                            <a-date-picker
                                @change="onChangeStart"
                                format="YYYY-MM-DD HH:mm:ss"
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"/>                     

                            <h4>Ends at</h4>
                            <a-date-picker
                                @change="onChangeEnd"
                                format="YYYY-MM-DD HH:mm:ss"
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"/>
                            <b-button @click="checkDate()">Next</b-button> 
                        </div> 
                    </transition>

                <h3>Where is the event located?</h3>
                <b-form-group
                    id="input-group-5">

                    <b-form-input
                    id="input-location"
                    v-model="event.location"
                    type="text"
                    placeholder="Enter location..."
                    required>
                    </b-form-input>
                </b-form-group>

                <b-button type="submit" variant="primary">Submit</b-button>
            </b-form>
        </div>
    </div>
</template>
<script>
import moment from 'moment'

    export default {
        data() {
            return {
                event: {},

                showDate: true,

                //Date handling
                x:'',
                today: moment(),
                dateFormat: 'DD/MM/YYYY',
                ends: '',
                errorDate: false
                //
            }
        },

        created() {
            //When component is created -> fetch event based on id given in routing params
            this.axios
                .get(`http://localhost:8000/api/events/${this.$route.params.id}`)
                .then((res) => {
                    this.event = res.data;
                });
        },
        methods: {
            editEvent() {
                //Edit an event based on filled in event then redirects to homepage
                this.axios
                    .patch(`http://localhost:8000/api/events/${this.$route.params.id}`, this.event)
                    .then((res) => {
                        this.$router.push({ name: 'home' });
                    });
            },

            moment,

            checkDate() {
            let response = "";
            this.axios
                .post('http://127.0.0.1:8000/api/checkDate', this.event)
                 .then(res => (
                    // this.showLocation = false,
                    // this.showEnd = true,
                    // this.showDate = false,
                    this.x =false,

                    this.response = res.data
                ))
                .catch((err) => {
                    this.errors.record(err.response.data);

                    console.log(err.response.data.errors['startEvent']);
                    console.log(err.response.data.errors['endEvent']);


                    this.errorDate = true;
       
                })
                .finally( () => this.loading = false)
        },
        //

        //Date handling
        moment,

        range(start, end) {
            const result = [];
            for(let i = start; i < end; i++) {
                result.push(i);
            }
            return result;
        },

        //Returns dates to disable
        disabledDate(current) {
            return current && current < moment().endOf('day');
        },

        //Executes when a date is selected and sets the selected date to event.startEvent (multiple day event)
        onChangeStart(date, dateString){
            this.event.startEvent = dateString;
            
        },

        //Executes when a date is selected and sets the selected date to event.endEvent (multiple day event)
        onChangeEnd(date, dateString){
            this.event.endEvent = dateString;
        },

        //Executes when a date is selected and sets the selected date to event.startEvent and event.endEvent (one day event)
        onChangeOne(date, dateString){
            this.event.startEvent = dateString;

            var endDate = dateString.substring(0,10);

            this.ends = endDate;
        },

        //Executes when a time is selected and appends the time to the date selected in onChangeOne() (one day event)
        onChangeOneTime(time, timeString){
            var endResultDate = this.ends + " " + timeString;

            this.event.endEvent = endResultDate;

            console.log(this.event.endEvent);
        }
        }
    }
</script>
<style scoped>

</style>
