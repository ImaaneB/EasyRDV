// =========================
// EASYRDV - JAVASCRIPT
// =========================


// =========================
// VARIABLES
// =========================

let selectedService = null;
let selectedTime = null;


// =========================
// ÉLÉMENTS HTML
// =========================

const services =
    document.querySelectorAll(".reservation-service");

const timeSlots =
    document.querySelectorAll(".time-slot");

const dateInput =
    document.querySelector("#appointment-date");

const confirmationButton =
    document.querySelector(".reservation-button");

const reservationConfirmation =
    document.querySelector(".reservation-confirmation");

const reservationSummary =
    document.querySelector("#reservation-summary");

const summaryService =
    document.querySelector("#summary-service");

const summaryDate =
    document.querySelector("#summary-date");

const summaryTime =
    document.querySelector("#summary-time");

const finalConfirmationButton =
    document.querySelector(".final-confirmation-button");

const reservationSuccess =
    document.querySelector("#reservation-success");

const successService =
    document.querySelector("#success-service");

const successDateTime =
    document.querySelector("#success-date-time");


// =========================
// DATE MINIMALE
// =========================

const today = new Date();

const year =
    today.getFullYear();

const month =
    String(
        today.getMonth() + 1
    ).padStart(2, "0");

const day =
    String(
        today.getDate()
    ).padStart(2, "0");

const todayFormatted =
    year + "-" + month + "-" + day;


// Vérifier que le champ date existe
// avant de le modifier
if (dateInput) {

    dateInput.min =
        todayFormatted;

}


// =========================
// SÉLECTION DES PRESTATIONS
// =========================

services.forEach(function (service) {

    service.addEventListener(
        "click",
        function () {

            services.forEach(
                function (otherService) {

                    otherService.classList.remove(
                        "selected"
                    );

                }
            );

            service.classList.add(
                "selected"
            );

            const radio =
                service.querySelector(
                    'input[type="radio"]'
                );

            if (radio) {

                radio.checked = true;

                selectedService =
                    radio.value;

            }

        }
    );

});


// =========================
// SÉLECTION DES CRÉNEAUX
// =========================

timeSlots.forEach(function (slot) {

    slot.addEventListener(
        "click",
        function () {

            timeSlots.forEach(
                function (otherSlot) {

                    otherSlot.classList.remove(
                        "selected"
                    );

                }
            );

            slot.classList.add(
                "selected"
            );

            selectedTime =
                slot.textContent.trim();

        }
    );

});


// =========================
// PREMIÈRE CONFIRMATION
// =========================

if (
    confirmationButton &&
    dateInput &&
    reservationSummary &&
    summaryService &&
    summaryDate &&
    summaryTime
) {

    confirmationButton.addEventListener(
        "click",
        function () {


            // =========================
            // VÉRIFICATION PRESTATION
            // =========================

            if (selectedService === null) {

                alert(
                    "Veuillez choisir une prestation."
                );

                return;

            }


            // =========================
            // VÉRIFICATION DATE
            // =========================

            if (dateInput.value === "") {

                alert(
                    "Veuillez choisir une date."
                );

                return;

            }


            // =========================
            // VÉRIFICATION DATE PASSÉE
            // =========================

            if (
                dateInput.value < todayFormatted
            ) {

                alert(
                    "Vous ne pouvez pas choisir une date passée."
                );

                return;

            }


            // =========================
            // VÉRIFICATION CRÉNEAU
            // =========================

            if (selectedTime === null) {

                alert(
                    "Veuillez choisir un créneau."
                );

                return;

            }


            // =========================
            // NOM DE LA PRESTATION
            // =========================

            let serviceName = "";


            if (
                selectedService === "coupe-homme"
            ) {

                serviceName =
                    "Coupe homme";

            }

            else if (
                selectedService === "barbe"
            ) {

                serviceName =
                    "Barbe";

            }

            else if (
                selectedService === "coupe-barbe"
            ) {

                serviceName =
                    "Coupe + Barbe";

            }


            // =========================
            // FORMATAGE DE LA DATE
            // =========================

            const dateParts =
                dateInput.value.split("-");

            const formattedDate =
                dateParts[2] + "/" +
                dateParts[1] + "/" +
                dateParts[0];


            // =========================
            // REMPLIR LE RÉCAPITULATIF
            // =========================

            summaryService.textContent =
                serviceName;

            summaryDate.textContent =
                formattedDate;

            summaryTime.textContent =
                selectedTime;


            // =========================
            // AFFICHER LE RÉCAPITULATIF
            // =========================

            reservationSummary.style.display =
                "block";


            // =========================
            // DESCENDRE VERS
            // LE RÉCAPITULATIF
            // =========================

            reservationSummary.scrollIntoView({
                behavior: "smooth"
            });

        }
    );

}


// =========================
// CONFIRMATION DÉFINITIVE
// =========================

if (
    finalConfirmationButton &&
    summaryService &&
    summaryDate &&
    successService &&
    successDateTime &&
    reservationSummary &&
    reservationConfirmation &&
    reservationSuccess
) {

    finalConfirmationButton.addEventListener(
        "click",
        function () {


            // =========================
            // RÉCUPÉRER LES INFORMATIONS
            // =========================

            const serviceName =
                summaryService.textContent;

            const formattedDate =
                summaryDate.textContent;


            // =========================
            // REMPLIR LA CONFIRMATION
            // =========================

            successService.textContent =
                serviceName;

            successDateTime.textContent =
                formattedDate +
                " à " +
                selectedTime;


            // =========================
            // CACHER LE RÉCAPITULATIF
            // =========================

            reservationSummary.style.display =
                "none";


            // =========================
            // CACHER LE PREMIER BOUTON
            // =========================

            reservationConfirmation.style.display =
                "none";


            // =========================
            // AFFICHER LA CONFIRMATION
            // =========================

            reservationSuccess.style.display =
                "block";


            // =========================
            // DESCENDRE VERS
            // LA CONFIRMATION
            // =========================

            reservationSuccess.scrollIntoView({
                behavior: "smooth"
            });

        }
    );

}