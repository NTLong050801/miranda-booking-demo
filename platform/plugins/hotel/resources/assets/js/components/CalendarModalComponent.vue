<template>
    <div>
        <div id="dates-calendar" class="dates-calendar"></div>

        <div id="modal-calendar" class="modal fade">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="form_modal_calendar form-horizontal" novalidate onsubmit="return false">
                            <div class="form-group">
                                <label >{{ __('room_availability.status') }}</label>
                                <br>
                                <label ><input type="checkbox" true-value="1" false-value="0" v-model="form.active"> {{ __('room_availability.is_available') }}</label>
                            </div>
                            <div class="row">
                                <div class="col-md-6" v-show="form.active">
                                    <div class="form-group">
                                        <label for="price-input">{{ __('room_availability.price') }}</label>
                                        <input type="number" id="price-input" v-model="form.price" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6" v-show="form.active">
                                    <div class="form-group">
                                        <label for="number-of-rooms">{{ __('room_availability.number_of_rooms') }}</label>
                                        <input type="number" id="number-of-rooms" v-model="form.number_of_rooms" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('room_availability.close') }}</button>
                        <button type="button" class="btn btn-primary" @click="saveForm">{{ __('room_availability.save_changes') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                form: {
                    id: '',
                    price: '',
                    start_date: '',
                    end_date: '',
                    enable_person: 0,
                    min_guests: 0,
                    max_guests: 0,
                    active: 0,
                    number_of_rooms: 1
                },
                formDefault: {
                    id: '',
                    price: '',
                    start_date: '',
                    end_date: '',
                    enable_person: 0,
                    min_guests: 0,
                    max_guests: 0,
                    active: 0,
                    number_of_rooms: 1
                },
                onSubmit: false,
                calendar: null,
            }
        },
        methods: {
            show: function (form) {
                $('#modal-calendar').modal('show');
                this.onSubmit = false;

                if (typeof form != 'undefined') {
                    this.form = Object.assign({}, form);

                    if (form.start_date) {
                        $('.modal-title').text(moment(form.start_date).format('MM/DD/YYYY'));
                    }
                }
            },
            hide: function () {
                $('#modal-calendar').modal('hide');
                this.form = Object.assign({}, this.formDefault);
            },
            saveForm: function () {
                let _self = this;
                if (this.onSubmit) {
                    return;
                }

                if (!this.validateForm()) {
                    return;
                }

                $('#modal-calendar').find('.btn-primary').addClass('button-loading');

                this.onSubmit = true;
                $.ajax({
                    url: $('div[data-get-room-availability-url]').data('get-room-availability-url'),
                    data: this.form,
                    dataType: 'json',
                    method: 'POST',
                    success: res => {
                        if (!res.error) {
                            if (this.calendar) {
                                this.calendar.refetchEvents();
                            }
                            _self.hide();
                            Botble.showSuccess(res.message);
                        } else {
                            Botble.showError(res.message);
                        }
                        _self.onSubmit = false;
                        $('#modal-calendar').find('.btn-primary').removeClass('button-loading');
                    },
                    error: () => {
                        _self.onSubmit = false;
                        $('#modal-calendar').find('.btn-primary').removeClass('button-loading');
                    }
                });
            },
            validateForm: function () {
                if (!this.form.start_date) {
                    return false;
                }

                return this.form.end_date;
            },
        },
        created: function () {
            let _self = this;
            this.$nextTick(function () {
                $(_self.$el).on('hide.bs.modal', function () {
                    this.form = Object.assign({}, this.formDefault);
                });
            })
        },
        mounted() {
            let calendarEl;

            calendarEl = document.getElementById('dates-calendar');
            if (this.calendar) {
                this.calendar.destroy();
            }

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'title',
                },
                navLinks: true, // can click day/week names to navigate views
                editable: false,
                dayMaxEvents: false, // allow "more" link when too many events
                events: {
                    url: $('div[data-get-room-availability-url]').data('get-room-availability-url'),
                },
                loading: isLoading => {
                    if (!isLoading) {
                        $(calendarEl).removeClass('loading');
                    } else {
                        $(calendarEl).addClass('loading');
                    }
                },
                select: arg => {
                    this.show({
                        start_date: moment(arg.start).format('YYYY-MM-DD'),
                        end_date: moment(arg.end).format('YYYY-MM-DD'),
                    });
                },
                eventClick: info => {
                    let form = Object.assign({}, info.event.extendedProps);
                    form.start_date = moment(info.event.start).format('YYYY-MM-DD');
                    form.end_date = moment(info.event.start).format('YYYY-MM-DD');
                    this.show(form);
                },
                eventRender: info => {
                    $(info.el).find('.fc-title').html(info.event.title);
                }
            });

            this.calendar.render();
        }
    }
</script>
