<div class="bookwize theme-d">

    <div class="loader" id="Loader">
         <div class="bg"></div>
         <section class="inner">
        <div class="container-fluid loader-content">
            <header class="loader-header" data-bind="text: title">Please wait ...</header>
            <div data-bind="visible: showError" style="display:none">
                <div class="container">
                    <div class="row">
                        <div class="alert alert-danger">
                            <h3 style="text-transform:capitalize;" data-bind="html: message().severity"></h3>
                            <p data-bind="html: message().text"></p>
                        </div>
                        <div data-bind="visible: message().severity == 'critical'">
                            <p class="loader-reload" data-bind="click: reload">
                                Click here to start over
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="loader-footer">
            <p>POWERED BY</p>
            <img style="width:160px;" alt="logo"
                 src="https://ibe.blob.core.windows.net/images/bookwize-logo.png">
        </footer>
        <!-- </section> -->
    </div>

    <div id="Currency" data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/currency.html"></div>
    <div id="Header" data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/header.html"></div>

    <div id="ReservationContainer" class="reservation-container">
        <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/rateplan.html" id="Rateplan">
        </section>
        <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/step1_%theme%.html" id="Step1">
        </section>
        <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/step2_%theme%.html" id="Step2">
        </section>
        <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/step3_%theme%.html" id="Step3">
        </section>
        <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/step4_%theme%.html" id="Step4">
        </section>
    </div>

    <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/my-reservation/index.html"
             id="MyReservation"></section>
    <section data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/member/index.html" id="Membership"></section>

    <div id="Response"><?php include __DIR__.'/external/jcc-response.php'; ?></div>

    <footer data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/footer.html" id="Footer" class="main-footer"></footer>
</div>

<div id="RoomPopUp" class="modal fade popup" tabindex="-1" role="dialog" aria-labelledby="RoomPopUp" data-template="<?php echo BW_PUBLIC_URL; ?>public/partials/templates/popup.html"></div>
