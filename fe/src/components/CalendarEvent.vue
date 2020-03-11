<template>
  <section class="calendar-event">
    <h1>Calendar Events</h1>
    <b-container>
      <main class="b-calendar">
        <b-row>
          <b-col md="6">
            <div class="b-calendar__information">
              <div class="today">
                <div class="weekDay">
                  Today is
                </div>
                <div class="day">
                  {{ selectedDayAndMonth.day }}
                </div>
                <div class="month mb-2">
                  {{ selectedDayAndMonth.month | capitalize }}
                </div>
                 <div class="weekDay">
                  {{ selectedWeekDay | capitalize }}
                </div>
              </div>
              <hr class="mt-0 mb-0" />
              <div class="card-body px-lg-6 py-lg-6">
                <div class="mt-0 mb-3 text-left">
                  <span><b>ADD EVENT</b></span>
                </div>
                <b-form @submit="saveEvent">
                  <div class="mb-3 text-left">
                    <b-form-group label="Event Name" label-for="event-name">
                      <b-form-input
                        v-model="eventname"
                        placeholder="Enter event name"
                        id="event-name"
                        required
                      ></b-form-input>
                    </b-form-group>
                  </div>

                  <div class="row mb-3 text-left">
                    <div class="col-6">
                      <b-form-group label="From" label-for="from-date">
                        <b-form-input
                          v-model="fromdate"
                          type="date"
                          id="from-date"
                          placeholder="Enter From date"
                          @change="checkDateRange"
                          required
                        ></b-form-input>
                      </b-form-group>
                    </div>

                    <div class="col-6">
                      <b-form-group label="To" label-for="to-date">
                        <b-form-input
                          v-model="todate"
                          type="date"
                          id="to-date"
                          placeholder="Enter To date"
                          @change="checkDateRange"
                          required
                        ></b-form-input>
                      </b-form-group>
                    </div>
                  </div>

                  <div class="mb-3 text-center">
                    <b-form-checkbox-group
                      id="checkbox-group-days"
                      v-model="selectedDays"
                      :options="daysoptions"
                    ></b-form-checkbox-group>
                  </div>

                  <div class="mb-3 text-center">
                    <b-button
                      v-show="!saving"
                      variant="primary"
                      size="lg"
                      type="submit"
                      >Save</b-button
                    >
                    <b-button
                      v-show="saving"
                      variant="light"
                      size="lg"
                      type="submit"
                      ><b-spinner
                        v-show="saving"
                        variant="primary"
                        label="Spinning"
                      ></b-spinner>
                    </b-button>
                  </div>
                </b-form>
              </div>
            </div>
          </b-col>
          <b-col md="6">
            <div class="b-calendar__calendar">
              <div class="b-calendar__header">
                <b-row>
                  <b-col class="year text-right" align-h="end">
                    <span>{{ year }}</span>
                  </b-col>
                </b-row>
                <b-row align-v="center">
                  <b-col class="text-left" align-h="start">
                    <b-button
                      id="subtractMonthBtn"
                      class="arrow arrow-left"
                      variant="light"
                      @click="subtractMonth"
                    >
                      <font-awesome-icon icon="angle-left" />
                    </b-button>
                    <b-tooltip target="subtractMonthBtn">
                      {{ previousMonthAsString | capitalize }}
                    </b-tooltip>
                  </b-col>
                  <b-col class="text-center" align-h="center">
                    <span class="month">{{ month }}</span>
                    <br />
                    <a
                      href="#"
                      id="goTodayLink"
                      @click="goToday"
                      v-show="
                        !todayInCurrentMonthAndYear || !todayIsEqualSelectDate
                      "
                    >
                      Today
                    </a>
                    <b-tooltip
                      target="goTodayLink"
                      v-show="
                        !todayInCurrentMonthAndYear || !todayIsEqualSelectDate
                      "
                    >
                      Back to today
                    </b-tooltip>
                  </b-col>
                  <b-col
                    class="text-right d-flex flex-row-reverse"
                    align-h="end"
                  >
                    <b-button
                      id="addMonthBtn"
                      class="arrow arrow-right"
                      variant="light"
                      @click="addMonth"
                    >
                      <font-awesome-icon icon="angle-right" />
                    </b-button>
                    <b-tooltip target="addMonthBtn">
                      {{ nextMonthAsString | capitalize }}
                    </b-tooltip>
                  </b-col>
                </b-row>
              </div>
              <div class="b-calendar__weekdays">
                <div class="weekday" v-for="(day, index) in days" :key="index">
                  <strong>{{ day }}</strong>
                </div>
              </div>
              <div class="b-calendar__dates">
                <div
                  class="date text-right"
                  :class="{
                    today: date.today,
                    blank: date.blank,
                    selectevent: date.eventDay,
                    'no-border-right': date.key % 7 === 0
                  }"
                  v-for="date in dateList"
                  :key="date.key"
                  :data-date="date.date"
                >
                  <span class="day">{{ date.dayNumber }}</span>
                  <div class="additional" v-show="date.eventName">
                    <span class="month" v-show="date.eventName">{{
                      date.eventName
                    }}</span>
                  </div>
                  <span class="weekday">{{ date.weekDay }}</span>
                  <div class="additional" v-show="date.additional">
                    <span class="year" v-show="date.additional.year">{{
                      date.additional.year
                    }}</span>
                    <span class="month" v-show="date.additional.month">{{
                      date.additional.month
                    }}</span>
                  </div>
                </div>
              </div>
            </div>
          </b-col>
        </b-row>
      </main>
    </b-container>
  </section>
</template>

<script lang="js">
  const Moment = require('moment');
  const MomentRange = require('moment-range');
  const moment = MomentRange.extendMoment(Moment);

  export default  {
    name: 'calendar-event',
    data() {
      return {
        today: moment(),
        dateContext: moment(),
        selectedDate: moment(),
        days: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        selectedDays: [],
        eventname: '',
        todate: '',
        fromdate: '',
        saving: false,
        calendarList: []
      }
    },

    computed: {
        daysoptions: function() {
           let daysoptions = [];
            this.days.forEach((day,i) => {
              let value = i+1;
              if(value > 6){
                value = 0;
              }
              let option = { text: day, value: value}
              daysoptions.push(option);
            });
            return daysoptions;
        },
        year: function() {
            return this.dateContext.format("Y");
        },

        month: function() {
            return this.dateContext.format("MMMM");
        },

        daysInMonth: function() {
            return this.dateContext.daysInMonth();
        },

        currentDate: function() {
            return this.dateContext.get("date");
        },

        firstDayOfMonth: function() {
            let firstDay = moment(this.dateContext).subtract(this.currentDate,"days");
            return firstDay.weekday();
        },

        previousMonth: function() {
            return moment(this.dateContext).subtract(1, "month");
        },
        previousMonthAsString: function() {
            return this.previousMonth.format("MMMM");
        },
        nextMonth: function() {
            return moment(this.dateContext).add(1, "month");
        },
        nextMonthAsString: function() {
            return this.nextMonth.format("MMMM");
        },

        daysInPreviousMonth: function() {
            return this.previousMonth.daysInMonth();
        },
        daysFromPreviousMonth: function() {
            let daysList = [];
            let count = this.daysInPreviousMonth - this.firstDayOfMonth;
            while (count < this.daysInPreviousMonth) {
                count++;
                daysList[count] = count;
            }

            return daysList.filter(function() {
                return true;
            });
        },

        dateList: function() {
          if(this.calendarList.length <= 0){
            return this.setCalendarList();
          }else{
            return this.setCalendarEvent();
          }

        },

        initialDate: function() {
            return this.formattingDay(this.today.get("date"));
        },
        initialMonth: function() {
            return this.today.format("MMMM");
        },
        initialYear: function() {
            return this.today.format("Y");
        },
        todayInCurrentMonthAndYear: function() {
            return (
                this.month === this.initialMonth &&
                this.year === this.initialYear
            );
        },
        selectedDayAndMonth: function() {
            let dayAndMonth = this.selectedDate.format("D MMMM");
            dayAndMonth = dayAndMonth.split(" ");
            dayAndMonth = {
                day: dayAndMonth[0],
                month: dayAndMonth[1]
            };

            return dayAndMonth;
        },
        selectedWeekDay: function() {
            return this.selectedDate.format("dddd");
        },
        todayIsEqualSelectDate: function() {
            return (
                this.selectedDate.format("YYYYMMDD") ===
                this.today.format("YYYYMMDD")
            );
        }
    },
    methods: {
        goToDate: function(moment){
           this.calendarList = [];
           this.dateContext = moment;
        },

        addMonth: function() {
            this.clearForm();
            this.dateContext = this.nextMonth;
        },
        subtractMonth: function() {
            this.clearForm();
            this.dateContext = this.previousMonth;
        },
        setSelectedDate: function(moment) {
            this.selectedDate = moment;
        },
        goToday: function() {
            this.clearForm();
            this.selectedDate = this.today;
            this.dateContext = this.today;
        },
        formattingDay(day) {
            return ("0" + day).slice(-2);
        },
        getWeekDay(day) {
            let index = day;
            if (index > 7) {
                index %= 7;
            }
            index = index === 0 ? 6 : index - 1;
            return this.days[index];
        },

        setCalendarList: function(){
            let $this = this;
            let dateList = [];

            let previousMonth = this.previousMonth;
            let nextMonth = this.nextMonth;

            //dates for display
            let formattedCurrentMonth = this.dateContext.format("MM");
            let formattedCurrentYear = this.year;
            let formattedPreviousMonth = previousMonth.format("MM");
            let formattedPreviousYear = previousMonth.format("Y");
            let formattedNextMonth = nextMonth.format("MM");
            let formattedNextYear = nextMonth.format("Y");

            //counters
            let countDayInCurrentMonth = 0;
            let countDayInPreviousMonth = 0;

            //filling in dates from the previous month
            this.daysFromPreviousMonth.forEach(function(dayFromPreviousMonth) {
                countDayInCurrentMonth++;
                countDayInPreviousMonth++;

                let formattedDay = $this.formattingDay(dayFromPreviousMonth);
                let previousMonth =
                    $this.daysFromPreviousMonth.length ===
                    countDayInPreviousMonth
                        ? $this.previousMonthAsString
                        : false;
                let previousYear =
                    formattedCurrentYear !== formattedPreviousYear &&
                    $this.daysFromPreviousMonth.length ===
                        countDayInPreviousMonth
                        ? formattedPreviousYear
                        : false;
                let additional = {
                    month: previousMonth,
                    year: previousYear
                };

                if (!previousMonth && !previousYear) {
                    additional = false;
                }

                let completeDate = moment(
                        formattedPreviousYear +
                            formattedPreviousMonth +
                            formattedDay);
                let dayOfWeek = completeDate.weekday();

                dateList[countDayInCurrentMonth] = {
                    key: countDayInCurrentMonth,
                    dayNumber: formattedDay,
                    date:
                        formattedDay +
                        "." +
                        formattedPreviousMonth +
                        "." +
                        formattedPreviousYear,
                    blank: true,
                    eventDay: false,
                    eventName: null,
                    dayOfWeek:dayOfWeek,
                    today: false,
                    additional: additional,
                    weekDay: false,
                    moment: moment(
                        formattedPreviousYear +
                            formattedPreviousMonth +
                            formattedDay
                    )
                };
            });

            //filling in dates from the current month
            while (countDayInCurrentMonth < this.firstDayOfMonth + this.daysInMonth) {
                countDayInCurrentMonth++;

                let day = countDayInCurrentMonth - countDayInPreviousMonth;
                let weekDay = this.getWeekDay(countDayInCurrentMonth);
                let formattedDay = this.formattingDay(day);
                let completeDate = moment(
                        formattedCurrentYear +
                            formattedCurrentMonth +
                            formattedDay);
                let dayOfWeek = completeDate.weekday();

                dateList[countDayInCurrentMonth] = {
                    key: countDayInCurrentMonth,
                    dayNumber: formattedDay,
                    date:
                        formattedDay +
                        "." +
                        formattedCurrentMonth +
                        "." +
                        formattedCurrentYear,
                    blank: false,
                    eventDay: false,
                    eventName: null,
                    dayOfWeek: dayOfWeek,
                    today:
                        formattedDay === this.initialDate &&
                        this.todayInCurrentMonthAndYear,
                    additional: false,
                    weekDay: weekDay,
                    moment: moment(
                        formattedCurrentYear +
                            formattedCurrentMonth +
                            formattedDay
                    )
                };
            }

            let daysInNextMonth = 7 - (countDayInCurrentMonth % 7);
            let countDayInCurrentMonthSaved = countDayInCurrentMonth;
            let day = 0;

            //filling in dates from the next month
            if (daysInNextMonth < 7) {
                while (
                    countDayInCurrentMonth <
                    countDayInCurrentMonthSaved + daysInNextMonth
                ) {
                    countDayInCurrentMonth++;
                    day++;

                    let formattedDay = this.formattingDay(day);
                    let nextMonth = day === 1 ? this.nextMonthAsString : false;
                    let nextYear =
                        formattedCurrentYear !== formattedNextYear && day === 1
                            ? formattedNextYear
                            : false;
                    let additional = {
                        month: nextMonth,
                        year: nextYear
                    };

                    if (!nextMonth && !nextYear) {
                        additional = false;
                    }

                    dateList[countDayInCurrentMonth] = {
                        key: countDayInCurrentMonth,
                        dayNumber: formattedDay,
                        date:
                            formattedDay +
                            "." +
                            formattedNextMonth +
                            "." +
                            formattedNextYear,
                        blank: true,
                        today: false,
                        additional: additional,
                        weekDay: false,
                        moment: moment(
                            formattedNextYear +
                                formattedNextMonth +
                                formattedDay
                        )
                    };
                }
            }
            this.calendarList = dateList;
            return dateList.filter(function() {
                return true;
            });
        },

        saveEvent:function(evt) {
          evt.preventDefault()

          if(this.selectedDays.length <= 0){
            for (let i = 0; i <= 6; i++) {
              this.selectedDays.push(i);
            }
          }

          let form = {
            event_name : this.eventname,
            from : this.fromdate,
            to : this.todate,
            days : JSON.stringify(this.selectedDays)
          };

          this.setCalendarEvent();
          this.saving = false;
           let currentObj = this;
            this.axios.post('http://localhost:8000/api/event/create', form)
            .then(function (response) {
                currentObj.saving = false;
                currentObj.output = response.data;
                if(currentObj.output.code == 200){
                  currentObj.toastFire('Successfully saved event','success');
                }else{
                  currentObj.toastFire('Failed to save event','error');
                }

            })
            .catch(function (error) {
                currentObj.output = error;
                currentObj.toastFire('Failed to save event','error');
            });
        },

        setCalendarEvent: function() {
            let eventname = this.eventname;
            let fromdate = moment(this.fromdate).format('YYYYMMDD');
            let todate = moment(this.todate).format('YYYYMMDD');
            let selected_days = this.selectedDays;
            let dateA = moment(this.fromdate, "YYYYMMDD");
            let dateB = moment(this.todate, "YYYYMMDD");

            if(dateA.diff(dateB) > 0){
              this.toastFire('From date must not be later than to date','error');
            }else{
              let final_selection = [];
              let range = moment.range(fromdate, todate);
              for (let dts of range.by('days')) {
                  let comparedt = dts.format('YYYYMMDD');
                  this.dateList.forEach((date,i) => {
                    this.dateList[i].eventDay = false;
                    this.dateList[i].eventName = null;
                    let momentdate = moment(date.moment).format('YYYYMMDD');
                    if(comparedt === momentdate){
                        selected_days.forEach(day => {
                          if(day == date.dayOfWeek){
                              final_selection.push(date);
                          }
                      });
                      }
                  });
                }

                this.dateList.forEach((date,i) => {
                  final_selection.forEach(fs => {
                    if(date.key == fs.key){
                      this.dateList[i].eventDay = true;
                      this.dateList[i].eventName = eventname;
                    }
                  });
                });
              }

              return this.dateList;
        },

        clearForm: function() {
          this.eventname = '';
          this.todate = null;
          this.fromdate = null;
          this.selectedDays = [];
          this.calendarList = [];
        },

        checkDateRange: function(){
          let dateA = moment(this.fromdate, "YYYYMMDD");
          let dateB = moment(this.todate, "YYYYMMDD");

          if(dateA.diff(dateB) > 0){
            this.todate = null;
            this.fromdate = null;
            this.toastFire('From date must not be later than to date','error');
          }else{
             this.goToDate(moment(this.fromdate));
          }
        },

        toastFire: function(message,icon){
          const Toast = this.$swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
              toast.addEventListener('mouseenter', this.$swal.stopTimer)
              toast.addEventListener('mouseleave', this.$swal.resumeTimer)
            }
          })

          Toast.fire({
            icon: icon,
            title: message
          })
        }
    },
    filters: {
        capitalize: function(value) {
            if (!value) return "";
            value = value.toString();
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
    }

}
</script>

<style scoped lang="scss">
$font-family-base: "HelveticaNeue-Light", "Helvetica Neue Light",
  "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
$font-weight-base: 300;
$font-size: 1.125em;
$line-height: 1.3;

body {
  font-family: $font-family-base;
  font-weight: $font-weight-base;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-size: $font-size;
  line-height: $line-height;
}

.b-calendar {
  display: flex;
  align-items: center;
  margin: 2.5em 0;
  &__information {
    background-color: rgba(194, 194, 194, 0.2);
    border-radius: 1.2rem 0 0 1.2rem;
    height: 100%;
    .today {
      flex-direction: column;
      padding-top: 3em;
      .weekDay {
        font-size: 1.2em;
        font-weight: 100;
        padding-bottom: 0.5em;
      }
      .day {
        font-size: 5.5em;
        font-weight: 600;
        line-height: 1;
      }
      .month {
        font-size: 2em;
        font-weight: 200;
        line-height: 1;
      }
    }
  }
  &__calendar {
    min-height: 40rem;
  }
  &__header {
    margin-bottom: 2rem;
    .month {
      font-size: 1.7em;
      font-weight: 200;
      text-transform: capitalize;
    }
    .year {
      font-size: 1.7em;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .arrow {
      background: transparent;
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 2.5rem;
      width: 2.5rem;
      &:hover {
        cursor: pointer;
      }
      &-left {
        i {
          transform: translateX(-10%);
        }
      }
      &-right {
        i {
          transform: translateX(10%);
        }
      }
    }
  }
  &__weekdays {
    display: flex;
    margin-bottom: 1.25rem;
    .weekday {
      width: calc(100% / 7);
      padding: 0.25rem 0.5rem;
    }
  }
  &__dates {
    display: flex;
    flex-wrap: wrap;
    position: relative;
    &:after {
      content: "";
      position: absolute;
      bottom: 0;
      background-color: #fff;
      height: 1px;
      width: 100%;
      z-index: 1;
    }
    .date {
      border-right: 1px solid rgba(0, 0, 0, 0.05);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      font-weight: 200;
      min-height: 4.5rem;
      padding: 0.25rem 0.5rem;
      position: relative;
      width: calc(100% / 7);
      &.blank {
        background-color: rgba(0, 0, 0, 0.02);
        color: rgba(0, 0, 0, 0.2);
      }
      &.no-border-right {
        border-right: none;
      }
      &.today {
        background-color: rgba(44, 73, 136, 0.2);
      }
      &.selectevent {
        background-color: rgba(231, 79, 231, 0.2);
      }
      .weekday {
        display: none;
      }
      .additional {
        font-size: 0.75em;
        position: absolute;
        bottom: 0.25rem;
        left: 0.5rem;
        .year {
          padding-right: 0.25rem;
          font-size: 0.75em;
        }
      }
    }
  }
}

.b-footer {
  padding: 15px 0;
}

@media (max-width: 800px) {
  .b-calendar__information {
    min-height: auto;
    padding-top: 2rem;
    padding-bottom: 2rem;
    border-radius: 2.5rem 2.5rem 0 0;
    .today {
      padding-top: 0;
    }
  }
}

@media (max-width: 480px) {
  .b-calendar {
    &__weekdays {
      display: none;
    }
    &__dates {
      .date {
        width: 100%;
        text-align: left !important;
        border: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        &.blank {
          display: none;
        }
        .weekday {
          display: block;
          margin-left: 0.25rem;
        }
      }
    }
  }
}
</style>
