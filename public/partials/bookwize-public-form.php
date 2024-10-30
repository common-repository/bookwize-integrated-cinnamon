<div class="step1-content bookwize bookwize-integrated-form" data-bind="with:step1">
        <div data-bind="if: hotel()">
            <div class="main-content">
               
                    <div>
                        <input type="hidden" name="r" data-guests/>
                        <div data-bind="visible: hasMessage()">
                            <div data-bind="if: parseInt(message().id()) != 3001" class="message">
                                <p data-bind="css: 'alert alert-' + (message().severity() == 'error' ? 'danger' : message().severity())">
                                    <span data-bind="translate: message().text()"></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                </p>
                            </div>
                        </div>
                        <div data-bind="if: !bookingProcess().isValidStay">
                            <p class="alert alert-danger">
                                <span data-bind="text: IBE.Utils.translate('invalidCheckOutDate')"></span>
                            </p>
                        </div>

                        <div data-bind="visible: !isValidStay()">
                            <div class="alert alert-warning">
                                <span data-bind="text: IBE.Utils.translate('invalidDateRange')"></span>
                            </div>
                        </div>
                        <div class="grid grid--narrow" data-bind="if: requestedRooms().length > 0">

                            <div class="grid__item one-sixth palm--one-whole tab--one-whole nopadding">
                                <div class="form-group">
                                    <label for="CheckIn" data-bind="text: IBE.Utils.translate('checkIn')" class="bookwize-form-row-label"></label>

                                    <div class="datepicker-holder">
                                        <div class="datepicker-holder small-row bookwize-form-background">
                                            <input data-bind="datepickerWithRange: checkIn, availability: hotel().availability, bindMinDate: minCheckInDate(), datepickerOptions:{monthsToShow: 1, pickerClass: 'small-picker alignLeft'}, lang: lang, theme:'cinamon'" readonly="readonly" id="CheckIn" class="bookwize-form-input" required="required" data-arrow-offset="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid__item one-sixth palm--one-whole tab--one-whole nopadding">
                                <div class="form-group">
                                    <label for="CheckOut" data-bind="text: IBE.Utils.translate('checkOut')" class="bookwize-form-row-label"></label>

                                    <div class="datepicker-holder">
                                        <div class="datepicker-holder small-row bookwize-form-background">
                                            <input data-bind="datepickerWithRange: checkOut, availability: hotel().availability, bindMinDate: minCheckOutDate(), datepickerOptions:{monthsToShow: 1, pickerClass: 'small-picker alignLeft'}, lang: lang,  theme:'cinamon'" readonly="readonly" id="CheckOut" class="bookwize-form-input" required="required" data-arrow-offset="190">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid__item palm--one-whole tab--one-whole nopadding" data-bind="css: { 'two-sixths' : requestedRooms()[0].guests.length > 1, 'one-sixth' : requestedRooms()[0].guests.length === 1 }">
                                    <div data-bind="with: requestedRooms()[0]">
                                        <div class="request-room grid">
                                                <!-- ko foreach: guests -->
                                                <div class="grid__item palm--one-whole tab--one-whole nopadding" data-bind="css: { 'one-half' : $parent.guests.length === 2, 'one-whole' : $parent.guests.length === 1 , 'one-third' : $parent.guests.length === 3, 'one-forth' : $parent.guests.length === 4}">
                                                    <div class="form-group">
                                                        <!-- ko if: $parent.guests.length > 1 -->
                                                        <label data-bind="text: IBE.Utils.translate('guestType.' + ageCategory())" class="bookwize-form-row-label"></label>
                                                        <!-- /ko -->
                                                        <!-- ko if: $parent.guests.length == 1 -->
                                                        <label data-bind="text: IBE.Utils.translate('guests')" class="bookwize-form-row-label"></label>
                                                        <!-- /ko -->
                                                        <span data-bind="if: label() != ''" class="guest-category-age">
                                                (<span data-bind="text: label"></span>)
                                            </span>
                                                        <select class="bookwize-form-input" data-bind="select2: {allowClear: true, minimumResultsForSearch: -1 },options: options, value: pax"></select>
                                                    </div>
                                                </div>
                                                <!-- /ko -->

                                                <!-- ko if: guests.length > 3 -->
                                                <div class="clearfix"></div>
                                                <!-- /ko -->

                                                <div data-bind="visible: step1.requestedRooms().length > 1, css: { 'col-xs-2' : guests.length < 4 }"
                                                     class="text-center">
                                                    <div class="form-group">
                                                        <!-- ko if: guests.length < 4 -->
                                                        <label>&nbsp;</label>
                                                        <br class="hidden-xs" />
                                                        <!-- /ko -->
                                                        <button type="button" class="btn-remove-room no-outline"
                                                                data-bind="click: step1.removeRequestedRoom.bind($data), attr: {title : IBE.Utils.translate('removeRoom') }">
                                                            <i class="fa fa-times-circle"></i>
                                                            <span data-bind="translate: 'remove'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                            </div>
                            <!--ko if: boardTypes().length > 1 && IBEConfig.theme !== 'f' -->
                            <div class="grid__item one-sixth palm--one-whole tab--one-whole nopadding">
                                    <div class="board-type">


                                            <div  data-bind="if: requestedRooms().length > 0">
                                                <label for="promoCode" data-bind="text: IBE.Utils.translate('selectMealPlan')" class="bookwize-form-row-label"></label>
                                                    <select class="bookwize-form-input" data-bind="select2: {allowClear: true, minimumResultsForSearch: -1 }, options: boardTypes, value: boardType, optionsValue: 'boardType', optionsText: 'name'"></select>


                                                    <div data-bind="if: board">
                                                        <div data-bind="visible: board().description != ''">
                                                            <div data-bind="html: board().description"></div>
                                                        </div>
                                                    </div>

                                            </div>

                                    </div>
                            </div>
                            <!-- /ko-->

                            <div class="grid__item one-sixth palm--one-whole tab--one-whole nopadding">
                                <div class="step1-submit-holder text-center">
                                    <button type="submit" class="btn btn-primary bookwize-integrated-form-button bookwize-button bookwize-book-button"
                                            data-bind="text: IBE.Utils.translate('book'), click: function(){step1.submit($data)}, enable: allowSubmit"></button>
                                </div>
                            </div>
                        </div>

                    </div>
             
            </div>

        </div>
</div>
