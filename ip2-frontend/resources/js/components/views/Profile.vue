<template>
  <div>
    <a-layout>
      <a-layout-header style="background: white; height: 120px">
        <div style="float: left" class="profilewrap">
          <a-avatar :size="86" icon="user" :src="path" />
          <a-button
            type="danger"
            shape="circle"
            class="plusbutton"
            icon="plus"
            v-b-modal.modal-1
          />
        </div>

        <b-modal
          ref="uploadModal"
          id="modal-1"
          title="Upload a profile picture"
          hide-footer
        >

        
          <form @submit.prevent="upload">
            <input id="file" accept="image/jpg" @change="handleOnChange" type="file" />
            <label for="file">
                <span>Only .jpg extensions are allowed!</span>
            </label>   
            <br />
            <br />
            <div style="float: right">
              <b-button type="submit">Save picture</b-button>
              <b-button @click="hideModal" variant="danger">Cancel</b-button>
            </div>
          </form>
        </b-modal>

        <h1 style="float: left; padding: 15px">
          {{ user.firstName }} {{ user.lastName }}
        </h1>
      </a-layout-header>
      <a-layout>
        <a-layout-content style="background: white; padding: 15px">
          <h3>Your information</h3>
          <a-descriptions bordered>
            <a-descriptions-item label="Email">
              {{ user.email }}
            </a-descriptions-item>
            <a-descriptions-item label="Birthday">
              {{ user.birthday }}
            </a-descriptions-item>
            <a-descriptions-item label="Study">
              {{ user.study }}
            </a-descriptions-item>
          </a-descriptions>

          <br />
          <div>
            <h2>My events</h2>
            <div class="event" v-for="myEvent in myEvents" :key="myEvent.id">
              <router-link :to="{ name: 'event', params: { id: myEvent.id } }">
                <b-card class="event-card">
                  <div class="card-heading absolute top-3 left-3">
                    <h3 class="title">{{ myEvent.name }}</h3>
                  </div>

                  <div class="absolute date">
                    <div
                      class="top-date absolute inset-x-0 top-0 h-12 bg-red-500"
                    >
                      <span
                        class="text-white font-bold text-2xl absolute top-2 left-6 right-6"
                        >{{ getDay(myEvent.startEvent) }}</span
                      >
                    </div>
                    <div
                      class="bottom-date absolute inset-x-0 bottom-0 top-12 left-6 right-5 h-8"
                    >
                      <span class="text-black font-bold text-l">{{
                        getMonth(myEvent.startEvent)
                      }}</span>
                    </div>
                  </div>
                </b-card>
              </router-link>
            </div>
          </div>
        </a-layout-content>
      </a-layout>
    </a-layout>
  </div>
</template>
<script>
export default {
  data() {
    return {
      user: {},

      myEvents: {},

      image: "",

      path: "",
    };
  },
  created() {
    this.axios
      .get(`${this.$api}/api/users/3`)
      .then((response) => {
        // handle success
        this.user = response.data;

        this.path = `storage/images/${this.user.id}.jpg`;
      })
      .catch(function (error) {
        // handle error
        console.log(error);
      });

    //Show events
    this.axios
      .get(`${this.$api}/api/showByUser/3`)
      .then((response) => {
        // handle success
        this.myEvents = response.data;
      })
      .catch(function (error) {
        // handle error
        console.log(error);
      });
  },
  methods: {
    hideModal() {
      this.$refs["uploadModal"].hide();
    },
    getDay(date) {
      var date = date.substring(8, 10);

      return date;
    },
    getMonth(date) {
      var date = date.toString().substring(5, 7);

      switch (date) {
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

    handleOnChange(e) {
      this.image = e.target.files[0];
    },

    upload() {
      const formData = new FormData();

      formData.set("image", this.image);
      formData.set("user", this.user.id);

      axios
        .post("/upload", formData)
        .then((response) => {
          // handle success
          console.log("Upload done!");

          window.location.reload();
        })
        .catch(function (error) {
          // handle error
          console.log(error);
        });
    },
  },
};
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

.profilewrap {
  display: inline-block;
  position: relative;
}
.plusbutton {
  position: absolute;
  bottom: 0;
  right: 0;
  padding-bottom: 4px;
}
</style>
