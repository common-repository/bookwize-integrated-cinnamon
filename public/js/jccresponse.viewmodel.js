JCCResponseViewModel = function () {
    var self = this;
    IBE.Utils.deleteAuthenticationCookie();
    self.authorizationNumber = ko.observable('');
    self.merchantId = ko.observable('');
    self.acqId = ko.observable('');
    self.responseCode = ko.observable('');
    self.reasonCode = ko.observable('');
    self.reasonDescr = ko.observable('');
    self.refNumber = ko.observable('');
    self.paddedCardNo = ko.observable('');
    self.signature = ko.observable('');
    self.reservationId = ko.observable('');
    self.status = ko.observable('');
    self.myReservation = ko.observable(null);
    self.ready = ko.observable(false);
    self.statusUpdated = false;
    self.events = new ko.subscribable();
    self.hotel = ko.observable();
    self.googleMap = ko.observable();
    self.visible = ko.observable(false);
    ko.utils.extend(self, new IBE.ViewModels.LanguagePartialViewModel(self));

    self.status.subscribe(function (newValue) {

        self.fetchReservation(newValue);

    });

    self.init = function () {

        // remove previous login
        // IBE.Utils.deleteAuthenticationCookie();
        $.when(self.languagePartialInit()).then(function (response1) {


            if (self.status() === '') {
                self.events.notifySubscribers(true, 'error');


            } else {

                self.events.notifySubscribers(true, 'ready');
            }

        });


    };
    self.events.subscribe(function (error) {
        IBEStepsMain.setStep(1);
    }, this, 'error');

    self.events.subscribe(function (ready) {
        self.ready(true);
    }, this, 'ready');
    
    self.fetchReservation = function (newValue) {

        var email = Cache.read('authenticationKey');
        if (email) {
            email = atob(email);
        } else {
            IBEStepsMain.setStep(1);
            $('#Header').show();
            return;
        }

        var auth = {
            email: email,
            code: self.reservationId()
        };

        var reservation = new IBE.ViewModels.MyReservation(auth);
        reservation.init();
        reservation.events.subscribe(function (status) {
            self.hotel(reservation.hotel());
            self.googleMap(reservation.googleMap());
            self.myReservation(reservation);
            self.setConfirmed();

        }, this, 'ready');
        reservation.events.subscribe(function (error) {

            IBEStepsMain.setStep(1);
        }, this, 'error');


    };
    self.setConfirmed = function () {

        if (self.statusUpdated) {
            return;
        }

        if (self.status() == 1) {
            self.confirmAction('ConfirmedReservation');

        } else {
            self.confirmAction('CancelledReservationPaymentExpired');
        }


        self.statusUpdated = true;
    };

    self.cancelCode = ko.observable('');
    self.confirmAction = function (newStatus, event) {
        var payment = ko.utils.arrayFirst(self.myReservation().reservation().payments(), function (item) {
            return moment().diff(item.dateToPay, 'days') == 0 && item.paymentType === 'ExternalGateway';
        });
        var paymentId = '';
        if (payment) {
            paymentId = payment.id;
        }
        var payment = {
            reservationId: self.myReservation().reservation().id,
            amount: self.myReservation().reservation().totalCost().toFixed(2),
            transactionId: self.refNumber(),
            merchantId: self.merchantId(),
            notes: self.reasonDescr(),
            transactionLog: ' paymentTotal:' + self.myReservation().reservation().totalCost().toFixed(2) + ' authorizationNumber:' + self.authorizationNumber() + ' acqId:' + self.acqId() + ' responseCode:' + self.responseCode() + ' reasonCode:' + self.reasonCode() + 'paddedCardNo: ' + self.paddedCardNo() + 'signature:' + self.signature(),
            id: paymentId,
        }
        var data = {
            id: self.myReservation().reservation().id,
            newStatus: newStatus,
            creditCard: [],
            cancellationNotes: self.myReservation().reservation().cancellationNotes(),
            payment: payment
        };

        var hasErrors = false;
        switch (newStatus) {
            case 'CancelledReservation':
            case 'CancelledRequest':
                if (self.cancelCode() != self.myReservation().reservation().code) {
                    var message = {
                        id: 5001,
                        severity: 'error',
                        text: IBE.Utils.translate('wrongReservationCode')
                    };
                    self.events.notifySubscribers(message, 'error');
                    hasErrors = true;
                }
                break;
            case 'ConfirmedReservation':
                break;
            case 'CancelledReservationPaymentExpired':
                break;
        }

        if (hasErrors) {
            return false;
        }

        //self.clearMessage();

        // change reservation status
        IBE.Utils.request({
            type: 'POST',
            data: ko.toJSON(data),
            url: IBE.Config.apiBaseUrl + '/hotels/reservation/update/?apikey=' + IBE.Config.apiKey,
        })
            .done(function (response) {
                self.cancelCode('');
                self.myReservation().reservation(new IBE.Models.Reservation(response));
                self.events.notifySubscribers(true, newStatus);
                self.visible(true);
                //debugger;

                // var auth = {
                //     email: response.customer.email,
                //     code: response.code
                // };
                // IBEStepsMain.setStep(4, auth);
            })
            .fail(function (response) {
                var msg = {
                    id: 5002,
                    severity: 'error',
                    text: IBE.Utils.translate('updateReservationFailed')
                };
                self.events.notifySubscribers(msg, 'error');
            });

            
    };
};
