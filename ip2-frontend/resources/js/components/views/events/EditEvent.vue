<template>
  <div>
    <div>
      <h1>Edit an event</h1>
    </div>

    <div>
      <b-form>
        <h3>Name of the event</h3>
        <a-alert
          v-if="errorName.required"
          message="The name field is required."
          type="error"
          banner
          style="margin-bottom: 8px"
        />
        <a-alert
          v-if="errorName.max_size"
          message="The name field must not be greater than 30 chararcters."
          type="error"
          banner
          style="margin-bottom: 8px"
        />

        <a-alert
          v-if="errorName.min_size"
          message="The name must be at least 3 characters."
          type="error"
          banner
          style="margin-bottom: 8px"
        />
        <a-alert
          v-if="errorName.regex"
          message="The name must only contain letters, numbers, dashes and underscores."
          type="error"
          banner
          style="margin-bottom: 8px"
        />
        <b-form-group id="input-group-1">
          <b-form-input
            id="input-name"
            v-model="event.name"
            type="text"
            placeholder="Enter name (max. 30 characters)"
          >
          </b-form-input>
        </b-form-group>

        <h3>Description for the event</h3>
        <a-alert
          v-if="errorDescription"
          message="The description field is required."
          type="error"
          banner
          style="margin-bottom: 8px"
        />
        <b-form-group id="input-group-2">
          <b-form-textarea
            id="input-description"
            v-model="event.description"
            type="text"
            placeholder="Enter description (max. 50 characters)"
            rows="3"
            max-rows="6"
          >
          </b-form-textarea>
        </b-form-group>

        <!-- Date/Time -->
        <transition name="slide-fade">
          <div v-if="showDate">
            <h3>What type of event is it?</h3>
            <b-form-group>
              <b-form-radio-group plain>
                <b-form-radio v-model="x" name="oneday-radios" value="one"
                  >One day event</b-form-radio
                >
                <b-form-radio v-model="x" name="multdays-radios" value="mult"
                  >Multiple days event</b-form-radio
                >
              </b-form-radio-group>
            </b-form-group>
          </div>
        </transition>

        <a-alert
          v-if="errorDate"
          message="The date fields are all required."
          type="error"
          banner
          style="margin-bottom: 8px"
        />

        <a-alert
          v-if="errorEndDateIsNotGreater"
          message="The end date must be a date after the start date."
          type="error"
          banner
          style="margin-bottom: 8px"
        />

        <transition name="slide-fade">
          <div v-if="x === 'one'">
            <h4>Starts at</h4>
            <a-date-picker
              @change="onChangeOne"
              format="YYYY-MM-DD HH:mm:ss"
              :disabled-date="disabledDate"
              :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"
              :placeholder="event.startEvent"
            />

            <h4>Ends at</h4>
            <a-time-picker :placeholder="getTime(event.endEvent)" format="HH:mm:ss" @change="onChangeOneTime" />
          </div>
        </transition>

        <transition name="slide-fade">
          <div v-if="x === 'mult'">
            <h4>Starts at</h4>
            <a-date-picker
              @change="onChangeStart"
              format="YYYY-MM-DD HH:mm:ss"
              :disabled-date="disabledDate"
              :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"
              :placeholder="event.startEvent"
            />

            <h4>Ends at</h4>
            <a-date-picker
              @change="onChangeEnd"
              format="YYYY-MM-DD HH:mm:ss"
              :disabled-date="disabledDate"
              :show-time="{ defaultValue: moment('00:00', 'HH:mm:ss') }"
              :placeholder="event.endEvent"
            />
          </div>
        </transition>

        <br />

        <h3>Where is the event taking place?</h3>
        <a-alert
          v-if="errorLocation"
          message="The location field is required."
          type="error"
          banner
          style="margin-bottom: 8px"
        />
        <b-form-group id="input-group-5">
          <b-form-input
            id="input-location"
            v-model="event.location"
            type="text"
            placeholder="Enter location..."
          >
          </b-form-input>
        </b-form-group>

        <b-button @click="deleteEvent()" variant="danger">Delete</b-button>
        <b-button @click="editEvent()" variant="primary">Submit</b-button>
      </b-form>
    </div>
  </div>
</template>
<script>
import moment from "moment";
import _ from "lodash";

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
      event: {},

      showDate: true,

      //Date handling
      x: "",
      today: moment(),
      dateFormat: "DD/MM/YYYY",
      ends: "",

      //

      errors: new Errors(),
      errorName: {
        required: false,
        max_size: false,
        min_size: false,
        regex: false,
      },
      errorDescription: false,
      errorLocation: false,
      errorEndDateIsNotGreater: false,
      errorDate: false,
    };
  },

  created() {
    //When component is created -> fetch event based on id given in routing params
    this.axios
      .get(`${this.$api}/api/events/${this.$route.params.id}`)
      .then((res) => {
        this.event = res.data;
      });
  },
  methods: {
    editEvent() {
      //Edit an event based on filled in event then redirects to homepage
      //this.checkEditInput();

      this.axios
        .patch(`${this.$api}/api/events/${this.$route.params.id}`, this.event)
        .then((res) => {
          this.$router.push({ name: "home" });
        })
        .catch((err) => {
          //Record the errors 
          this.errors.record(err.response.data);
          
          //Get the error object
          let errObj = err.response.data.errors;
          
          //Loop over the error object
          //Depending on the message inside the error Object show the corresponding error message
          _.forEach(errObj, (r) => {
            let result = Object.values(r);
            console.log(result[0]);

            if (result[0] === "The name field is required.") {
              this.errorName.required = true;
              this.errorName.max_size = false;
              this.errorName.regex = false;
              this.errorName.min_size = false;
            } else if (result[0] === "The name format is invalid.") {
              this.errorName.max_size = false;
              this.errorName.required = false;
              this.errorName.regex = true;
              this.errorName.min_size = false;
            } else if (
              result[0] === "The name must be at least 3 characters."
            ) {
              this.errorName.max_size = false;
              this.errorName.required = false;
              this.errorName.regex = false;
              this.errorName.min_size = true;
            } else if (
              result[0] === "The name must not be greater than 30 characters."
            ) {
              this.errorName.max_size = true;
              this.errorName.required = false;
              this.errorName.regex = false;
              this.errorName.min_size = false;
            } else if (result[0] === "The description field is required.") {
              this.errorDescription = true;
            } else if (
              result[0] === "The end event must be a date after start event."
            ) {
              this.errorDate = false;
              this.errorEndDateIsNotGreater = true;
            } else if (
              result[0] ===
                "The end event field is required." ||
              result[0] === 
                "The start event field is required."
            ) {
              this.errorDate = true;
              this.errorEndDateIsNotGreater = false;
            } else if (result[0] === "The location field is required.") {
              this.errorLocation = true;
            }
          });
        })
        .finally(() => (this.loading = false));
    },

    deleteEvent() {  
      this.axios
        .delete(`${this.$api}/api/events/${this.$route.params.id}`)
        .then((response) => {
          // let i = this.event.map(data => data.id).indexOf(id);
          // this.event.splice(i, 1);
          console.log(response.data);

          this.$router.push({ name: "home" });
        });
    },

    //Date handling
    moment,

    getTime(date) {
        var date = date.substring(11, 16);
        //2021-11-20 08:21:01
        return date;
    },

    range(start, end) {
      const result = [];
      for (let i = start; i < end; i++) {
        result.push(i);
      }
      return result;
    },

    //Returns dates to disable
    disabledDate(current) {
      return current && current < moment().endOf("day");
    },

    //Executes when a date is selected and sets the selected date to event.startEvent (multiple day event)
    onChangeStart(date, dateString) {
      this.event.startEvent = dateString;
    },

    //Executes when a date is selected and sets the selected date to event.endEvent (multiple day event)
    onChangeEnd(date, dateString) {
      this.event.endEvent = dateString;
    },

    //Executes when a date is selected and sets the selected date to event.startEvent and event.endEvent (one day event)
    onChangeOne(date, dateString) {
      this.event.startEvent = dateString;

      var endDate = dateString.substring(0, 10);

      this.ends = endDate;
    },

    //Executes when a time is selected and appends the time to the date selected in onChangeOne() (one day event)
    onChangeOneTime(time, timeString) {
      var endResultDate = this.ends + " " + timeString;

      this.event.endEvent = endResultDate;

      console.log(this.event.endEvent);
    },
  },
};
</script>
<style scoped>
</style>
