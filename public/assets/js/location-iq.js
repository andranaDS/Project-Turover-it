class LocationIq {
    constructor(input, options) {
        this.inputSearch = input;
        this.options = options;

        this.initEvents();
    }

    initEvents() {
        this.inputSearch.addEventListener('input', this.change.bind(this));
    }

    change(e) {
        e.preventDefault();
        const input = e.target;
        const value = input.value;
        if (value.length >= 3) {
            // TODO: add timeout
            axios.get(input.getAttribute('api-autocomplete'), {
                params: {
                    search: value
                },
            })
            .then(function(response) {
                const data = response.data;
                if (data.length >= 0) {
                    this.buildChoices(data);
                } else {
                    this.clearChoices();
                }
            }.bind(this));
        } else {
            this.clearChoices();
        }
    }

    buildChoices(data) {
        this.clearChoices();
        const choices = [];
        data.forEach(d => {
            const choice = document.createElement('span');
            choice.classList.add('choice');
            const choicesData = Object.keys(d).map((key) => [key, d[key]]);
            choicesData.forEach(cd => {
                if (cd[0] === 'label') {
                    choice.innerHTML = cd[1];
                }
                choice.setAttribute(cd[0], cd[1] || '');
            });
            choices.push(choice);
        });

        const choicesContainer = document.createElement('div');
        choicesContainer.classList.add('choices-container');
        choices.forEach(choice => {
            choicesContainer.appendChild(choice);
        })
        this.inputSearch.after(choicesContainer);

        this.initChoicesEvents()
    }

    initChoicesEvents() {
        [...document.querySelectorAll('.choice')].forEach(choice => {
            choice.addEventListener('click', this.clickChoice.bind(this));
        })
    }

    clickChoice(e) {
        const choice = e.target;
        const fields = ['streetNumber', 'streetName', 'locality', 'postalCode', 'adminLevel1', 'adminLevel2', 'country', 'countryCode', 'latitude', 'longitude']

        fields.forEach(field => {
            document.querySelector('input[name="'+this.options.hiddenFieldName+'['+field+']"]').value = choice.getAttribute(field.toLowerCase());
        })
        this.inputSearch.value = choice.innerHTML;
        
        this.clearChoices();
    }

    clearChoices() {
        const choicesContainer = document.querySelector('.choices-container');
        if (choicesContainer) choicesContainer.remove();
    }
}

(function () {
    window.addEventListener('load', () => {
        function init(className) {
            const inputs = document.querySelectorAll(className);
            if (inputs.length > 0) {
                inputs.forEach((input) => {
                    new LocationIq(input, {
                        hiddenFieldName: input.getAttribute('name').replace('[locationIqInput]', '')
                    });
                })
            }
        }

        init('.location-iq');

        const btnAdds = document.querySelectorAll('.location-iqs .field-collection-add-button');
        if (btnAdds.length > 0) {
            btnAdds.forEach((btnAdd) => {
                btnAdd.addEventListener('click', function() {
                    init('.location-iqs .location-iq');
                });
            })
        }
    });
}());
