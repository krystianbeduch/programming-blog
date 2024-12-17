$(document).ready(() => {

    const radioSetupLanguage = $("input[name='setup-language']");
    const radioSetupMode = $("input[name='setup-mode']");
    const button = $("#confirm-setup");
    const divWidget = $(".widget");
    const divLearn = $("#learn-words");
    button.hide();
    divLearn.hide();

    const configuration = () => {
        $(function() {
            $(".setup-inputs input").checkboxradio({
                icon: false
            });
        });



        let configLanguage = null, configMode = null;

        radioSetupLanguage.on("change", function() {
            configLanguage = $(this).val();
            checkEndConfigure();
        });

        radioSetupMode.on("change", function() {
            configMode = $(this).val();
            checkEndConfigure();
        });

        button.on("click", () => {
            setupWords();
        });

        const checkEndConfigure = () => {
            if (configLanguage && configMode) {
                button.show();
            }
        };
        const setupWords = () => {
            if (configMode === "learn") {
                // location.href = "learn.html";
                learnWordsMode(configLanguage);
            }
        };
    }

    configuration();

    const learnWordsMode = (language) => {
        divWidget.hide();
        divLearn.show();
        const pWordPl = $("#word-pl");
        const pWordEn = $("#word-en");
        const input = $("input#word");
        const words = ["jabłko", "apple", "pomarancza", "orange"];

        // while(true) {
            if (language === "pl") {
                pWordPl.text(words[0]);
                pWordEn.text(words[1]);
            }
        // }

    }




});