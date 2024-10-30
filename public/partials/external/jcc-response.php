<?php
if (isset($_POST['MerID'])) {
    $jccMerID = $_POST['MerID'];
    if (isset($_POST['AcqID'])) {
        $jccAcqID = $_POST['AcqID'];
    }
    if (isset($_POST['ResponseCode'])) {
        $jccResponseCode = intval($_POST['ResponseCode']);
    }
    if (isset($_POST['ReasonCode'])) {
        $jccReasonCode = intval($_POST['ReasonCode']);
    }
    if (isset($_POST['ReasonCodeDesc'])) {
        $jccReasonDescr = $_POST['ReasonCodeDesc'];
    }
    if (isset($_POST['ReferenceNo'])) {
        $jccRef = $_POST['ReferenceNo'];
    }
    if (isset($_POST['PaddedCardNo'])) {
        $jccPaddedCardNo = $_POST['PaddedCardNo'];
    }
    if (isset($_POST['ResponseSignature'])) {
        $jccSignature = $_POST['ResponseSignature'];
    }

    $jssSuccess = '';
    $jccAuthNo = 0;
    //for both response code and reason code
    if (isset($jccResponseCode) && isset($jccReasonCode)) {
        if ($jccResponseCode == 1 && $jccReasonCode == 1) {
            $jccAuthNo = $_POST['AuthCode'];
            $jssSuccess = true;
        }
    }
    //The parameters used for creating the JCC signature as stored on the merchant server
    $password = get_option('bw_jcc_password') ? get_option('bw_jcc_password') : '';
    $merchantID = get_option('bw_jcc_merchant') ? get_option('bw_jcc_merchant') : '';
    $acquirerID = get_option('bw_jcc_acquirer') ? get_option('bw_jcc_acquirer') : '';
    if (isset($_POST['OrderID'])) {
        $orderID = $_POST['OrderID'];
    }
    if (isset($orderID) && isset($jccResponseCode) && isset($jccReasonCode)) {
        $toEncrypt = $password . $merchantID . $acquirerID . $orderID . $jccResponseCode . $jccReasonCode;
    }
    $sha1Signature = sha1($toEncrypt);

    $expectedBase64Sha1Signature = base64_encode(pack("H*", $sha1Signature));
    if (isset($jccSignature)) {
        $verifyJCCSignature = ($expectedBase64Sha1Signature == $jccSignature);
    }
    ?>
    <script>
        response = new JCCResponseViewModel();
        setTimeout(function () {
            if (typeof response != 'undefined') {
                <?php  if (isset($orderID)) { ?>
                var orderid = <?php echo json_encode($orderID); ?> ;
                response.reservationId(orderid);
                <?php } ?>
                <?php  if (isset($jccAuthNo)) { ?>
                var authorizationNumber = <?php echo json_encode($jccAuthNo); ?> ;
                response.authorizationNumber(authorizationNumber);
                <?php } ?>
                <?php  if (isset($jccMerID)) { ?>
                var merchantId = <?php echo json_encode($jccMerID); ?> ;
                response.merchantId(merchantId);
                <?php } ?>
                <?php  if (isset($jccAcqID)) { ?>
                var acqId = <?php echo json_encode($jccAcqID); ?> ;
                response.acqId(acqId);
                <?php } ?>
                <?php  if (isset($jccResponseCode)) { ?>
                var responseCode = <?php echo json_encode($jccResponseCode); ?> ;
                response.responseCode(responseCode);
                <?php } ?>
                <?php  if (isset($jccReasonCode)) { ?>
                var reasonCode = <?php echo json_encode($jccReasonCode); ?> ;
                response.reasonCode(reasonCode);
                <?php } ?>
                <?php  if (isset($jccReasonDescr)) { ?>
                var reasonDescr = <?php echo json_encode($jccReasonDescr); ?> ;
                response.reasonDescr(reasonDescr);
                <?php } ?>
                <?php  if (isset($jccRef)) { ?>
                var refNumber = <?php echo json_encode($jccRef); ?> ;
                response.refNumber(refNumber);
                <?php } ?>
                <?php  if (isset($jccPaddedCardNo)) { ?>
                var paddedCardNo = <?php echo json_encode($jccPaddedCardNo); ?> ;
                response.paddedCardNo(paddedCardNo);
                <?php } ?>
                <?php  if (isset($jccSignature)) { ?>
                var signature = <?php echo json_encode($jccSignature); ?> ;
                response.signature(signature);
                <?php } ?>
                <?php  if (isset($jccResponseCode)) { ?>
                var status = <?php echo json_encode($jccResponseCode); ?> ;
                response.status(status);
                <?php } ?>
                if(typeof response!== 'undefined'){
                    response.init();
                }
            } else {
                IBEStepsMain.setStep(1);
            }
        }, 5000);
    </script>


<!-- ko ifnot:ready() -->
<div class="hp-wait text-center">
    <p data-bind="translate: 'pleaseWait'"></p>
</div>
<!-- /ko -->
<!-- ko if: ready() && myReservation() -->

<div data-bind="with: response.myReservation().reservation(), visible: response.visible()">
    <div class="clearfix">

        <section class="reservation">

            <header class="reservation-title text-center">

                <div class="hidden-print">
                    <!--ko ifnot: status() === 'NewReservationPaymentPending' -->
                    <span data-bind="translate: 'welcomeBack'"></span>
                    <small data-bind="text: customer.title, visible: customer.title() != 'Unknown' "></small>
                    <span data-bind="text: customer.fullName()"></span>
                    <!-- /ko -->
                    <!--ko if: status() === 'ConfirmedReservation'-->
                    <div data-bind="translate:'paymentSuccess'"
                            class="bookwize-info text-center confirmed"></div>
                    <aside class="bookwize-info text-center margin-top"
                            data-bind="css:{'confirmed': status() ==='ConfirmedReservation'}">
                        <p class="hidden-print"
                            data-bind="translate: 'reservationSuccessInfo',  formatData: [customer.email]"></p>
                    </aside>
                    <!-- /ko-->
                    <!--ko ifnot: status() === 'ConfirmedReservation'-->
                    <div data-bind="translate:'paymentFailed'"
                            class="bookwize-info text-center failed"></div>
                    <aside class="margin-top bookwize-info text-center failed">
                        <div class="label" data-bind="translate: 'status'"></div>
                        <div class="text reservation-status" data-bind="reservationStatus: status()">
                            <span data-bind="translate: 'reservationStatus.' + status()"></span>
                        </div>
                    </aside>
                    <!-- /ko-->

                </div>

            </header>

            <div class="bookwize-hidden-print">
                <div class="text-right">

                    <button id="btnPrint" class="bookwize-button no-outline" onclick="window.print();"><i
                                class="material-icons">&#xE8AD;</i></button>
                </div>
            </div>

            <!-- ko if: customer.resetHash !== '' && customer.resetHash !== null -->
            <header class="bookwize-step4-title text-center hidden-print margin-top">
                <small data-bind="translate: 'createMemberPasswordPrompt'"></small>
            </header>

            <div data-bind="with: $parent.membership, visible:$parent.membership.message().severity() !== 'success'"
                    class="margin-auto one-half">

                <div class="bookwize-row required bookwize-form-background"
                        data-bind="css:{'up':newPassword() !== '' && newPassword() !== undefined}">
                    <label for="newPassword" data-bind="translate: 'newPassword'"
                            class="bookwize-label"></label>
                    <input id="newPassword" data-bind="value: newPassword" type="password"
                            name="newPassword"
                            class="bookwize-form-input editable">
                </div>

                <div class="bookwize-row required  bookwize-form-background"
                        data-bind="css:{'up':retypePassword() !== '' && retypePassword() !== undefined}">
                    <label for="retypePassword" data-bind="translate: 'retypePassword'"
                            class="bookwize-label"></label>
                    <input id="retypePassword" data-bind="value: retypePassword" type="password"
                            name="retypePassword"
                            class="bookwize-form-input editable">
                    <div data-bind='component: {name: "passwordstrength-widget", params: { value: newPassword()}}'></div>
                </div>


                <div class="password-messages margin-top" data-bind="visible: newPassword() != null">

                    <ul data-bind="foreach: passwordMessage, visible: newPassword() != null ">
                        <li data-bind="translate: $data" class="password-messages-element"></li>
                    </ul>
                </div>
                <button class="bookwize-button bookwize-button-primary margin-top"
                        data-bind="click: createPassword, translate: 'createPassword', enable: isPasswordValid"></button>

            </div>
            <div data-bind="with: $parent.membership" class="margin-auto one-half">
                <div data-bind="visible: hasMessage(), with: message()">
                    <div class="bookwize-message margin-top">
                        <p data-bind="css: 'alert-' + (severity() == 'error' ? 'danger' : severity())">
                            <span data-bind="translate: text()"></span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- /ko-->
            <header class="bookwize-step4-title no-border margin-top"
                    data-bind="translate: 'reservationDetails'"></header>

            <div class="bookwize-step4-inner">

                <div class="grid">
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'bookingNumber'"></div>
                        <div class="bookwize-step4-text code" data-bind="text: code"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'pinCode'"></div>
                        <div class="bookwize-step4-text" data-bind="text: id"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'arrival'"></div>
                        <div class="bookwize-step4-text"
                                data-bind="date: checkIn, dateFormat: 'DD/MM/YYYY'"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'departure'"></div>
                        <div class="bookwize-step4-text"
                                data-bind="date: checkOut, dateFormat: 'DD/MM/YYYY'"></div>
                    </div>
                </div>
            </div>
            <header class="bookwize-step4-title" data-bind="translate: 'guestDetails'"></header>
            <div class="bookwize-step4-inner">
                <div class="grid">
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'label_firstName'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.firstName"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'label_lastName'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.lastName"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'email'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.email"></div>
                    </div>
                    <div class="grid__item one-half">
                        <div class="bookwize-step4-label" data-bind="translate: 'telephone'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.phone"></div>
                    </div>
                    <div class="grid__item one-half">
                        <!-- ko if: customer.address() !== ''-->
                        <div class="bookwize-step4-label" data-bind="translate: 'address'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.address"></div>
                        <!-- /ko-->
                    </div>
                    <div class="grid__item one-half">
                        <!-- ko if: customer.city() !== ''-->
                        <div class="bookwize-step4-label" data-bind="translate: 'city'"></div>
                        <div class="bookwize-step4-text" data-bind="text: customer.city"></div>
                        <!-- /ko-->
                    </div>
                </div>
            </div>


            <div class="bookwize-step4-inner">

                <section data-bind="foreach: rooms" class="bookwize-reservation-rooms">

                    <article class="bookwize-reservation-room">
                        <div class="grid">
                            <div class="grid__item lap--one-quarter one-whole">
                                <div class="bookwize-room-small-thumb"
                                        data-bind="backgroundImage: roomPhoto, backgroundImageOptions: {width: $root.roomImageWidth, height: $root.roomImageHeight, mode:'crop'}"></div>
                            </div>
                            <div class="grid__item lap--two-quarters one-whole">
                                <div class="bookwize-reservation-room-index">
                                    <span data-bind="translate: 'room'"></span> <span
                                            data-bind="text: $index()+1"></span>
                                </div>
                                <div class="bookwize-reservation-room-inner">
                                    <div class="bookwize-reservation-room-inner-label"
                                            data-bind="translate:'guests'"></div>
                                    <div class="bookwize-reservation-room-inner-text">
                                        <span data-bind="text: adults"></span>

                                        <!-- ko if: adults == 1 -->
                                        <span data-bind="translate: 'adult'"></span>
                                        <!-- /ko -->
                                        <!-- ko if: adults > 1 -->
                                        <span data-bind="translate: 'adults'"></span>
                                        <!-- /ko -->
                                        <!-- ko if: children === 1 -->
                                        ,<span data-bind="text: children"></span> <span
                                                data-bind="translate: 'child'"></span>
                                        <!--/ko-->
                                        <!-- ko if: children > 1 -->
                                        ,<span data-bind="text: children"></span> <span
                                                data-bind="translate: 'children'"></span>
                                        <!-- /ko -->
                                        <!-- ko if: infants > 0 -->
                                        ,<span data-bind="text: infants"></span> <span
                                                data-bind="translate: 'infants'"></span>
                                        <!-- /ko -->
                                    </div>
                                </div>
                                <div class="bookwize-reservation-room-inner">
                                    <div class="bookwize-reservation-room-inner-label"
                                            data-bind="translate:'room'"></div>
                                    <div class="bookwize-reservation-room-inner-text"
                                            data-bind="text: name"></div>
                                </div>
                                <div class="bookwize-reservation-room-inner">
                                    <div class="bookwize-reservation-room-inner-label"
                                            data-bind="translate:'mealplan'"></div>
                                    <div class="bookwize-reservation-room-inner-text"
                                            data-bind="text: board.name"></div>
                                </div>
                                <div class="bookwize-reservation-room-inner">
                                    <div class="bookwize-reservation-room-inner-label"
                                            data-bind="translate:'rateplan'"></div>
                                    <!-- ko if: ratePlanId == 0 -->
                                    <span class="bookwize-reservation-room-inner-text"
                                            data-bind="translate: 'standardRate'"></span>
                                    <!-- /ko -->
                                    <!-- ko if: ratePlanId > 0 -->
                                    <span class="bookwize-reservation-room-inner-text"
                                            data-bind="text: ratePlanName"></span>
                                    <!-- /ko -->
                                </div>
                            </div>
                            <div class="grid__item  lap--one-quarter one-whole">
                <span data-bind="money: totalCostBeforeDiscount"
                        class="bookwize-price bookwize-price-line-through text-right"></span>
                                <span data-bind="money: totalCost" class="bookwize-price text-right"></span>
                            </div>
                        </div>
                    </article>

                </section>
                <section class="bookwize-reservation-supplements"
                            data-bind="visible: supplements.length > 0">

                    <div data-bind="translate: 'extraSupplements'"
                            class="bookwize-reservation-supplements-title"></div>


                    <div data-bind="foreach: supplements">
                        <article class="bookwize-reservation-supplement">
                            <div class="grid">
                                <div class="grid__item two-thirds">
                                    <div class="bookwize-reservation-supplement-name">
                                        <span data-bind="text: name"></span>
                                        <span>&times;<span data-bind="text: quantity()"></span></span>
                                    </div>
                                </div>
                                <div class="grid__item one-third">
                                    <div class="bookwize-price text-right"
                                            data-bind="money: totalPrice()"></div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
                <aside class="bookwize-reservation-totals">

                    <div class="grid">
                        <div class="grid__item lap--one-half lap--push--one-half one-whole">
                            <div class="grid">
                                <div class="grid__item  lap--one-quarter">
                                    <span data-bind="translate: 'total'"></span> :
                                </div>
                                <div class="grid__item three-quarters text-right">
                                    <div class="bookwize-price bookwize-price-big">
                                        <div data-bind="money: totalCost"></div>

                                    </div>

                                    <div class="bookwize-tax"
                                            data-bind="translate:'allTaxesIncluded'"></div>
                                </div>

                            </div>
                        </div>

                    </div>

                </aside>
            </div>
            <section class="bookwize-step4-policy">

                <header data-bind="translate: 'cancellationPolicy'"></header>
                <div class="body" data-bind="html: reservationTerms"></div>

            </section>



                <header class="bookwize-step4-title" data-bind="translate: 'hotelInformation'"></header>

                <section class="bookwize-step4-inner no-padding" data-bind="with:$root">
                    <div class="google-map hidden-print">
                        <div class="bookwize-map" id="map" data-bind="map: googleMap"></div>
                    </div>
                    <div class="grid">
                        <div class="grid__item one-half">

                            <div class="bookwize-hotel-contact-info">
                                <div class="bookwize-hotel-contact-info-inner">
                                    <div class="bookwize-hotel-label" data-bind="translate: 'hotelDetails'"></div>

                                </div>
                                <div class="bookwize-hotel-contact-info-inner">
                                    <div class="bookwize-hotel-contact-info-label" data-bind="translate: 'address'"></div>
                                    <div class="bookwize-hotel-contact-info-text" data-bind="html: hotel().address.street"></div>

                                </div>
                                <div class="bookwize-hotel-contact-info-inner">
                                    <div class="bookwize-hotel-contact-info-label" data-bind="translate: 'telephone'"></div>
                                    <a class="bookwize-hotel-contact-info-text"
                                        data-bind="text: hotel().address.phone, attr:{href:'tel:' + hotel().address.phone}"></a>
                                </div>
                                <div class="bookwize-hotel-contact-info-inner">
                                    <div class="bookwize-hotel-contact-info-label" data-bind="translate: 'email'"></div>
                                    <a class="bookwize-hotel-contact-info-text"
                                        data-bind="text: hotel().email, attr:{href:'href:' + hotel().email}"></a>
                                </div>
                            </div>
                        </div>
                        <div class="grid__item one-half">

                            <div class="bookwize-hotel-contact-info">
                                <div class="bookwize-hotel-contact-info-inner">

                                    <a class="bookwize-hotel-contact-info-text" target="_blank"
                                        data-bind="attr:{'href': 'http://maps.google.com/maps?&z=10&q=' + hotel().address.latitude + '+' + hotel().address.longitude + '&ll=' + hotel().address.latitude + '+' + hotel().address.longitude}">
                                        <span data-bind="translate:'visitGoogleMaps'"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


        </section>


    </div>


</div>
<!-- /ko -->
<?php } ?>
