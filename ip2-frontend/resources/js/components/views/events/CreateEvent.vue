<template>
    <div>
        <b-container class="mx-auto">
             <div class="d-flex justify-content-center" style="margin-top: 10%">
                 <b-form @submit.prevent="addEvent">
                    <!-- Name -->
                    <div v-if="showName">
                        <h1>Enter a name for your event</h1>

                        <a-alert
                            v-if="errorName.required"
                            message="The name field is required."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />
                        <a-alert
                            v-if="errorName.max_size"
                            message="The name field must not be greater than 30 chararcters."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />
                        <a-alert
                            v-if="errorName.regex"
                            message="The name must only contain letters, numbers, dashes and underscores."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />
                        <a-alert
                            v-if="errorName.min_size"
                            message="The name must be at least 3 characters."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />


                        <b-form-group id="input-group-name">
                                <b-form-input
                                    id="input-name"
                                    v-model="event.name"
                                    type="text"
                                    placeholder="Enter name (max. 30 characters)"
                                    required>
                                </b-form-input>
                        </b-form-group>  

                        <b-button @click="checkName()">Next</b-button>                          
                    </div>

                    <!-- Description -->
                    <transition name="slide-fade">
                        <div v-if="showDescription">
                            <h1>Describe your event</h1>

                            <a-alert
                            v-if="errorDescription"
                            message="The description field is required."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />

                            <!-- <vue-editor v-model="event.description"></vue-editor> -->

                            <b-form-textarea
                                id="textarea"
                                v-model="event.description"
                                placeholder="Enter a description"
                                rows="3"
                                max-rows="6"
                            ></b-form-textarea>
                            
                            <br>

                            <b-button @click="checkDescription()">Next</b-button>
                        </div>
                    </transition>

                    <!-- Date/Time -->
                    <transition name="slide-fade">
                        <div v-if="showDate">
                            <h1>What type of event is it?</h1>
                            <a-alert
                                v-if="errorDate"
                                message="The date fields are all required."
                                type="error"
                                banner
                                style="margin-bottom:8px;" />
                            <a-alert
                                v-if="errorEndDateIsNotGreater"
                                message="The end date must be a date after start date."
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

                    <br><br>

                    <transition name="slide-fade">
                        <div v-if="x === 'one'">
                            <h3>Starts at</h3>
                            <a-date-picker
                                @change="onChangeOne"
                                format="YYYY-MM-DD HH:mm:ss"
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('12:00', 'HH:mm') }"/>

                            <h3>Ends at</h3>
                                <a-time-picker 
                                format="HH:mm:ss" 
                                @change="onChangeOneTime"/>

                            <b-button @click="checkDate()">Next</b-button> 
                        </div>
                    </transition>

                    <transition name="slide-fade">
                        <div v-if="x === 'mult'">
                            <h3>Starts at</h3>
                            <a-date-picker
                                @change="onChangeStart"
                                format="YYYY-MM-DD HH:mm:ss"
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('12:00', 'HH:mm') }"/>                     

                            <h3>Ends at</h3>
                            <a-date-picker
                                @change="onChangeEnd"
                                format="YYYY-MM-DD HH:mm:ss"          
                                :disabled-date="disabledDate"
                                :show-time="{ defaultValue: moment('12:00', 'HH:mm') }"/>
                            <b-button @click="checkDate()">Next</b-button> 
                        </div> 
                    </transition>

                    <!-- Location -->
                    <transition name="slide-fade">
                        <div v-if="showLocation">
                            <h1>Where does the event happen?</h1>
                            <a-alert
                            v-if="errorLocation"
                            message="The location field is required."
                            type="error"
                            banner
                            style="margin-bottom:8px;" />
                            <b-form-group id="input-group-loc">
                                <b-form-input
                                    id="input-location"
                                    v-model="event.location"
                                    type="text"
                                    placeholder="Enter a location"
                                    required>
                                </b-form-input>
                            </b-form-group>


                            <b-button @click="checkLocation()">Next</b-button>
                        </div>
                    </transition>    

                    <!-- Summary -->
                    <transition name="slide-fade">
                        <div v-if="showEnd">
                            <h1>Is this information correct?</h1>
                            <span> Your event is called <strong>{{ event.name }}</strong> </span> <br>
                            <span> Start the <strong>{{ event.startEvent }}</strong> and ends the <strong>{{ event.endEvent  }}</strong> </span> <br>
                            <span> Located at <strong>{{ event.location }}</strong> </span>

                            <br> <br>

                            <span> Description </span>
                            <strong><span style="width:400px;"> {{ event.description }} </span></strong>

                            <!-- <input type="hidden" name="user_id" :value="user.id"> -->
                            
                            <b-button variant="danger" @click="showNameForm()">Edit event</b-button>
                            <b-button type="submit" variant="primary">Create event</b-button>
                        </div>
                    </transition>  
                 </b-form>
             </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment';

//Class to record error and error message
class Errors {
  constructor() {
    this.errors = {};
  }

  //Get error message
  get(field) {
    if (this.errors[field]) {
      return this.errors[field][0];
    }
  }

  //Record the error
  record(errors) {
    this.errors = errors.errors;
  }
}

export default {
    data() {
        return {
            event: {

            },

            user: {},

            //Show components
            showName: true,
            showDescription: false,
            showLocation: false,
            showEnd: false,
            showDate: false,
            showDateOneDay: false,
            showDateMultDay: false,
            x:'',
            //

            //Date handling
            today: moment(),
            dateFormat: 'DD/MM/YYYY',
            ends: '',
            //

            //Errors
            errors: new Errors(),
            errorName: {
                required: false,
                max_size: false,
                regex: false,
                min_size: false
            },
            errorDescription: false,
            errorLocation: false,
            errorDate: false,
            errorEndDateIsNotGreater: false,
            //
        }
    },
    created() {
        this.axios
            .get(`${this.$api}/api/users/4`)
            .then((response) => {
                // handle success
                this.user = response.data;
                
                this.event.user_id = this.user.id;

            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
    },
    methods: {
        addEvent() {
            this.axios
                .post(`${this.$api}/api/events`, this.event)
                .then(response => (
                    this.$router.push({ name: 'home' })
                ))
                .catch(err => console.log(err))
                .finally( () => this.loading = false)
        },

        //Errorhandling
        checkName() {
            this.axios
                .post(`${this.$api}/api/checkName`, this.event)
                 .then(res => (
                    this.showEnd = false,          
                    this.showName = false,
                    this.showDescription = true,

                    this.response = res.data
                ))
                .catch((err) => {
                    this.errors.record(err.response.data);

                    console.log(err.response.data.errors['name'][0]);

                    if(err.response.data.errors['name'][0] === "The name field is required.") 
                    {
                        this.errorName.required = true;
                        this.errorName.size = false;
                        this.errorName.regex= false;
                        this.errorName.min_size = false;
                    } else if(err.response.data.errors['name'][0] === "The name must not be greater than 30 characters.")
                    {
                        this.errorName.size = true;
                        this.errorName.required = false;
                        this.errorName.regex= false;
                        this.errorName.min_size = false;
                    } else if(err.response.data.errors['name'][0] === "The name format is invalid.")
                    {
                        this.errorName.size = false;
                        this.errorName.required = false;
                        this.errorName.regex= true;
                        this.errorName.min_size = false;

                    } else if(err.response.data.errors['name'][0] === "The name must be at least 3 characters.")
                    {
                        this.errorName.size = false;
                        this.errorName.required = false;
                        this.errorName.regex= false;
                        this.errorName.min_size = true;
                    }  
                })
                .finally( () => this.loading = false)
        },

        checkDescription() {
            let response = "";
            this.axios
                .post(`${this.$api}/api/checkDescription`, this.event)
                 .then(res => (
                    this.showEnd = false,
                    this.showDescription = false,
                    this.showDate = true,

                    this.response = res.data
                ))
                .catch((err) => {
                    this.errors.record(err.response.data);

                    console.log(err.response.data.errors['description']);

                    this.errorDescription = true;
       
                })
                .finally( () => this.loading = false)
        },

        checkLocation() {
            let response = "";
            this.axios
                .post(`${this.$api}/api/checkLocation`, this.event)
                 .then(res => (
                    this.showLocation = false,
                    this.showEnd = true,

                    this.response = res.data
                ))
                .catch((err) => {
                    this.errors.record(err.response.data);

                    console.log(err.response.data.errors['location']);

                   
                    this.errorLocation = true;
       
                })
                .finally( () => this.loading = false)
        },
        

        checkDate() {
            let response = "";
            this.axios
                .post(`${this.$api}/api/checkDate`, this.event)
                 .then(res => (
                    // this.showLocation = false,
                    // this.showEnd = true,
                    this.showEnd = false,
                    this.showDate = false,
                    this.x =false,

                    this.showLocation = true,

                    this.response = res.data
                ))
                .catch((err) => {
                    this.errors.record(err.response.data);

                    console.log(err.response.data.errors['startEvent']);
                    console.log(err.response.data.errors['endEvent']);

                    if(err.response.data.errors['endEvent'][0]==="The end event must be a date after start event.")
                    {
                        this.errorEndDateIsNotGreater = true;
                        this.errorDate = false;
                    }
                    if(err.response.data.errors['endEvent'][0]==="The end event field is required." || err.response.data.errors['startEvent'][0]==="The start event field is required." )
                    {
                        this.errorDate = true;
                        this.errorEndDateIsNotGreater=false;
                    }
                })
                .finally( () => this.loading = false)
        },
        //


        //Show components
        showNameForm() {
            this.showEnd = false;
            this.showName = true;
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
        disabledDate(current) {
            return current && current < moment().endOf('day');
        },

        multDisabledDate(){
            var date = this.event.startEvent;

            var day = date.substring(0,10);
            
            
            return moment(day, 'YYYY-MM-DD')
        },

        onChangeStart(date, dateString){
            this.event.startEvent = dateString;
            
        },

        onChangeEnd(date, dateString){
            this.event.endEvent = dateString;
        },
        onChangeOne(date, dateString){
            this.event.startEvent = dateString;

            var endDate = dateString.substring(0,10);

            this.ends = endDate;
        },
        onChangeOneTime(time, timeString){
            var endResultDate = this.ends + " " + timeString;

            this.event.endEvent = endResultDate;

            console.log(this.event.endEvent);
        }
        //

    }
}
</script>
<style scoped>
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
