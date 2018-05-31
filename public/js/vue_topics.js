// NECESSARY TO ALLOW COMMUNICATION BETWEEN COMPONENTS
window.Event = new Vue();

// Vue.component('modal', {
//     template: '#modal-template'
// })

Vue.component('login-levels', {
    template: '#modal_template',
    // FIRST CALL
    props: ['missingEmailConfirmationMsg', 'missingMobileConfirmationMsg'],
    data(){
        return {
            showModal:true,
            reasons: []
        }
    },
    methods: {
        show(){
            vm = this;
            vm.showModal = true;
        },
        hide(){
            vm = this;

            vm.showModal = false;
        }

    },
    created(){
        var vm = this;
        Event.$on('supplyReasons',function(reasons){
            var reasonsArray = [];
            for(var key in reasons) {
                var reasonsList = reasons[key];
                for(var reasonKey in reasonsList) {
                    if(reasonKey === 'email_confirmed') {
                        reasonsArray.push({'title': vm.missingEmailConfirmationMsg });
                    }else if(reasonKey === 'mobile_number_confirmed') {
                        reasonsArray.push({'title': vm.missingMobileConfirmationMsg });
                    }else{
                        reasonsArray.push({'title': reasonsList[reasonKey]});
                    }
                }
            }
            vm.reasons = reasonsArray;
            vm.show();
        });
    },

    mounted(){
        var vm = this;
        vm.showModal = false;
    }
})

// MAP COMPONENT [COMUNICATES WITH THE TOPIC COMPONENT]
Vue.component('google-map', {

    template:'<div id="google-map" class="gmap"></div>', // THIS IS THE ONLY HTML NECESSARY

    props: { // PROPS NEED TO BE SENT FROM THE VIEW [e.g. TOPIC COMPONENT]
        'latitude': {
            type: Number,
            default: function(){
                return 38.718226
            }
        },
        'longitude': {
            type: Number,
            default: function(){
                return -9.133959
            }
        },
        'zoom': {
            type: Number,
            default: function(){
                return 8
            }
        }
    },

    data(){
        return {
            map:null,
            markers: []

        }
    },

    methods: {
        // CREATE MARKERS IN MAP ACCORDING TO LIST OF SUPPLIED GEOLOCATIONS
        redrawMarkers(){
            vm = this;
            for (var i = 0; i < vm.markers.length; i++) {
                var marker = new google.maps.Marker({
                    position: { lat: parseFloat( vm.markers[i].lat ), lng: parseFloat( vm.markers[i].long ) },
                    map: vm.map,
                });
            }
        }
    },

    created(){
        vm = this;
        // LISTEN IN THE loadedLocation IF SOMENONE PUBLISHES A LOCATION
        Event.$on('loadedLocation',function(address){
            vm.markers.push(address);
            vm.redrawMarkers();
        });
    },

    // FIRST CALL
    mounted(){
        vm = this;

        // INITIALIZE THE MAP WITH THE SUPPLIED [DEFAULT OR PROP] VALUES 
        vm.map = new google.maps.Map(document.getElementById('google-map'), {
            center: {lat: vm.latitude, lng: vm.longitude},
            zoom: vm.zoom
        });
    }

});


Vue.component('user', {

    template: '#user-bar',

    props: ['loggedUser'],

    data(){
        return {
            current_user: null,
            confirmed_email: true,
            showLoginBar: false
        }
    },

    mounted(){
        vm = this;
        if(vm.loggedUser !== 'null'){
            vm.current_user = JSON.parse(vm.loggedUser);
            vm.confirmed_email = vm.current_user.confirmed;
        }else{
            vm.showLoginBar = true;
        }
    }

});

Vue.component('cb', {

    template: '#cb-template',
    props: ['cbKey', 'type', 'currentLanguage', 'loggedUser', 'defaultImage', 'cbLayout'],

    data() {
        return {
            cb: null,
            title: null,
            votes: null,
            actions: [],
            voteKeys: null,
            loggedUser: null,
            contents: null,
            loading_topics: true,
            countTopics: 0,
            countTopicsTotal: 0,
            // defaultImage: {'url':this.defaultImage}, // THIS NEEDS TO BE A PROP

            topics:[],
            filters:[],
            filterStatus: null,
            definedOrder: null,
            parameters:[],
            showFilters: false,

            search:'', // VARIABLE TO BIND SEARCH V-MODEL

            // THIS LIST DEFINES THE PARAMETERS WE DON'T WANT TO SHOW AS "RAW" VALUE
            forbidden: ['images','files','google_maps'],

            height:150, //DEFAULT HEIGHT TO DOWNLOAD
            quality:65 //DEFAULT QUALITY TO DOWNLOAD


        };
    },
    computed: {
        // BINDED TOPICS TO SEARCH INPUT
        filteredTopics: function(){
            var vm = this;
            return vm.topics.filter((topic) => {
                    return topic.title.toLowerCase().match(vm.search.toLowerCase()) || topic.topic_number.toString() == vm.search.toString();
        });

        },
        strippedContent() {
            let regex = /(<([^>]+)>)/ig;
            return this.comment.content.rendered.replace(regex, "");
        }
    },

    methods: {
        _helperCheckValidDate(dateToTest, verify) {
            if (verify === 'begin') {
                return Date.now() > new Date(dateToTest);
            }
            if (verify === 'end') {
                return Date.now() < new Date(dateToTest);
            }
            return false;
        },
        // [HELPER FUNCTION] TO CHECK IF ARRAY CONTAINS VALUE
        _helper_inArray: function (needle, haystack, isString = false){

            var length = haystack.length;
            for (var i = 0; i < length; i++) {
                if(isString){
                    if (haystack[i] === needle) { // === FOR STRINGS
                        return true;
                    }
                }else{
                    if (haystack[i] == needle) {
                        return true;
                    }
                }
            }
            return false;
        },
        // [HELPER FUNCTION] FETCHS PARAMETERS ACCORDING TO CODE
        _helper_getParameteresAccordingToCode: function (parameters,code) {

            return items = parameters.filter(function (item) {
                return item.code === code;
            });

        },
        // [HELPER FUNCTION] RECEIVES A ARRAY OF ENCODED FILES AND BUILDS THE LIST
        helper_buildAccordingToParameterValue: function (parameters) {

            var items = [];
            for (var i = parameters.length - 1; i >= 0; i--) {
                var value = JSON.parse(parameters[i].pivot.value);
                if(value && value[0]){
                    var url = '/files/'+value[0].id+'/'+value[0].code;
                    var name = value[0].name;
                    var pieces = name.split(".");
                    var type = pieces[(pieces.length-1)];
                    items.push({'url':url,'name':name, 'type': type});
                }
            }
            return items;
        },
        // [HELPER FUNCTION] RECEIVES A CONTENT AND LIMITS IT'S CHARACTERS
        _helper_limitCharacters: function (content,limit){
            var limit = limit;
            return content.length > limit ? content.substring(0, limit) + '...' : content;
        },
        // [HELPER FUNCTION] RECEIVES A STRING SPLITS IT AND CONVERTS EACH VALUE TO A GIVEN FORMAT
        _helper_splitAndConvertItemsToGivenFormat: function (string,delimeter,format){

            var preparedValue = string.split(delimeter).map(function (x) {
                switch(format){
                    case 'float':
                        return parseFloat(x,10);
                        break;
                    case 'integer':
                        return parseInt(x,10);
                        break;
                }
            });
            return preparedValue;
        },

        // FILTER LIST OF TOPICS ACCORDING TO LIST OF PARAMETERS
        filterByParameter: function(option,parameter){
            var vm = this;

            var existed = false;
            vm.countTopics = 0;


            // FIND THE PARAMETER OPTION SELECTED AND ENABLE / DISABLE THE FILTER
            vm.parameters.map(function (parameter_item){
                if(parameter_item.id === parameter){
                    if(parameter_item._options){
                        parameter_item._options.map(function(option_item){
                            if(option_item.id == option){
                                if(option_item.selected){
                                    option_item.selected = false; // ENABLE FILTER
                                }else{
                                    option_item.selected = true; // DISABLE FILTER
                                }
                            }else{
                                option_item.selected = false;
                            }
                        });
                    }
                }
            });

            // CHECK IF THE USER WANTS TO ENABLE OR DISABLE THE FILTER
            // existed = vm.filters.filter((item) => {
            //         if(item.parameter_id == parameter && item.option_id == option){
            //     return true;
            // }
            // });

            // IF THE FILTER EXISTED THE USER WANTS TO DISABLE IT
            // if(existed[0]){
            //     console.log("existia");
            //
            //     // LET'S REMOVE IT FROM THE FILTERS ARRAY
            //     var newFilters = vm.filters.filter(function(item){
            //         console.log(item.parameter_id, parameter, item.option_id, option);
            //         if(parseInt(item.parameter_id) !== parseInt(parameter) && parseInt(item.option_id) !== parseInt(option)){
            //             console.log(2);
            //             return item;
            //         }
            //     });
            //
            //
            //     vm.filters = newFilters;
            // }else{

            var newFilter = {};
            if(vm.filters.option_id == option){
                newFilter = {};
            }else{
                // LET'S ADD THE NEW FILTER
                newFilter = {'parameter_id':parameter,'option_id':option};
            }
            // }

            vm.filters = newFilter;

            // GO THREW THE LIST OF TOPICS AND APPLY THE SELECTED FILTERS
            vm.topics.map(function(topic){

                // IF WE HAVE FILTERS

                if(Object.keys(vm.filters).length > 0){

                    // INITIALIZE BOOLEANS
                    var hasOption = false;
                    var hasParameter = false;
                    var hasAllSelectedFilters = false;

                    // HIDE ALL TOPICS
                    topic.visible = false;

                    // CHECK IF THIS TOPIC CONTAINS ALL THE SELECTED FILTERS
                    //for (var i = vm.filters.length - 1; i >= 0; i--) {

                    var filter_parameter = vm.filters.parameter_id;

                    // RETREIVE THE PARAMETER FROM THE TOPIC PARAMETERS LIST
                    hasParameter = topic.parameters.filter(function(item){
                        if(item.id == filter_parameter){
                            return item;
                        }
                    });

                    // IF THE TOPIC HAS PARAMETER
                    if(hasParameter[0]){

                        // IF THE TOPIC PARAMETER HAS OPTIONS
                        if(hasParameter[0]._options){

                            // FETCH THE OPTION
                            var filter_option = vm.filters.option_id;

                            hasOption = hasParameter[0]._options.filter(function(item){
                                if(item.id == filter_option){
                                    return item;
                                }
                            });

                            // IF THE TOPIC PARAMETER HAS THE SELECTED OPTION
                            if(hasOption[0]){
                                hasAllSelectedFilters = true;
                            }else{
                                console.log("não tem");
                                hasAllSelectedFilters = false; // IF DOESN'T ONE OF THE FILTERS LET'S BREAK THE CYCLE
                                // break;

                            }
                        }
                    }else{
                        hasAllSelectedFilters = false;
                        // break;
                    }
                    //}

                    // IF THE TOPIC HAS ALL THE SELECTED FILTERS LET'S MAKE IT VISIBLE
                    if(hasAllSelectedFilters){
                        vm.countTopics++;
                        topic.visible = true;
                    }

                }else{
                    vm.countTopics++;
                    // NO FILTERS SELECTED [BO RESTRICTIONS]
                    topic.visible = true;
                }
            });

            // FORCE THE UPDATE THE VIEW
            vm.$forceUpdate();
        },

        filterByStatus: function(statusTypeCode){
            var vm = this;
            vm.countTopics = 0;

            if(vm.filterStatus == statusTypeCode){
                vm.filterStatus = null;
            }else{
                vm.filterStatus = statusTypeCode;
            }

            if(vm.filterStatus != null && statusTypeCode !== 'all'){
                vm.topics.map(function(topic){
                    if(topic.status[0].status_type.code == statusTypeCode){
                        vm.countTopics++;
                        topic.visible = true;
                    }else{
                        topic.visible = false;
                    }
                });
            }else{
                vm.topics.map(function(topic){
                    vm.countTopics++;
                    topic.visible = true;
                });
            }

            vm.$forceUpdate();
        },
        //SORT TOPICS BY GIVEN ORDER
        orderTopics: function(order){
            var vm = this;
            vm.countTopics = 0;

            if(vm.definedOrder == order){
                vm.definedOrder = null;
            }else{
                vm.definedOrder = order;
            }

            if(order === 'number') {
                vm.topics.sort(function (a, b) {
                    return parseInt(a.topic_number) - parseInt(b.topic_number);
                });
            }
            if(order === 'votes') {
                vm.topics.sort(function (a, b) {
                    return parseInt(b.parameter_votes) - parseInt(a.parameter_votes);
                });
            }

            if(order === 'random') {
                vm.topics.sort(function(){return 0.5 - Math.random()});
            }

            vm.$forceUpdate();
        },

        formatDate: function(date){
            var vm = this;
        },
        frontEndDateFormat: function(date) {
            return moment(String(date)).format('DD/MM/YYYY');
        },

        supplyReasons: function(voteKey){
            var vm = this;

            if(vm.actions.vote){
                if(vm.actions.vote[voteKey]){
                    var reasons = vm.actions.vote[voteKey].missingAttributesPerLevel;
                    Event.$emit('supplyReasons',reasons);
                }
            }
        },
        showMissingLevels: function(actionCode){
            var vm = this;
            console.log(vm.actions[actionCode]);
            if(vm.actions[actionCode]){
                if(vm.actions[actionCode]){
                    var reasons = vm.actions[actionCode].missingAttributesPerLevel;
                    Event.$emit('supplyReasons',reasons);
                }
            }
        },
        // TRIGGER A TOPIC VOTE IN A SPECIFIC EVENT
        triggerVote: function(vote,topic,value){
            var vm = this;
            axios.post('/cb/voteInTopic', {
                'voteKey': vote.vote_key,
                'topicKey': topic.topic_key,
                'value': value,
                'cbKey': vm.cbKey
            }).then(function (response) {

                // WE EXPECT A SUCESS ALWAYS [NEED TO IMPROVE THIS]
                var result = JSON.parse(response.data);
                console.log(result);
                // ERROR IN VOTE METHOD
                if(result.errorMsg){
                    throw result.errorMsg;
                }

                vm.votes.map(function (vote_item){

                    // FIND THE VOTE
                    if(vote_item.vote_key == vote.vote_key){

                        // INCREASE / DECREASE THE USER VOTES IN THIS EVENT
                        vote_item.user_registered_votes += (result.vote == 1 ? 1 : -1);
                        vote.total_votes_allowed += (result.vote == 0 ? 1 : -1);
                        // SEARCH THE LIST OF TOPICS
                        vm.topics.map(function(topic_item){

                            //FIND THE TOPIC
                            if(topic_item.topic_key === topic.topic_key){

                                // CHANGE THE USER RELATION [VOTE/VOTED] TO THIS VOTE IN THIS TOPIC
                                topic_item.has_voted[vote_item.vote_key] = result.vote;
                                topic_item.total_votes[vote_item.vote_key] += (result.vote == 1 ? 1 : -1);
                            }
                        });
                    }
                });
                // FORCE THE UPDATE THE VIEW
                vm.$forceUpdate();

            }).catch(function (error) {
                alert(error);
                console.log(error);
            });
        },

        // LOAD CURRENT USER AVAILABLE ACTIONS
        loadAvailableActions: function () {
            var vm = this;
            // FETCH ACTIONS BY AJAX CALL
            axios.get('/cb/' + this.cbKey + '/getUserAvailableActions')
                .then(function (response) {

                    // SET THE AVAILABLE ACTIONS
                    vm.actions = response.data;

                    // LOAD VOTE EVENTS
                    vm.loadVotes();

                }).catch(function (error) {
                //DEAL WITH ERRORS
                console.log(error);
            });
        },

        // LOAD CURRENT PAD TOPICS
        loadTopics: function () {

            var vm = this;
            // FETCH THE TOPICS BY AJAX CALL
            axios.get('/cb/' + vm.cbKey + '/getPadTopics')
                .then(function (response) {
                    var topics = response.data;
                    vm.noTopics = true;
                    var monthNames = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun","Jul", "Ago", "Set", "Out", "Nov", "Dez"];
                    var template = vm.template === 'event' ? 'event' : 'default';
                    // MAP EACH RECEIVED TOPIC
                    topics.map(function (item) {
                        vm.noTopics = false;
                        // DEFAULT VALUES
                        item.files = [];
                        item.images = [];
                        item.has_voted = [];
                        item.vote_value = [];
                        item.vote_events = [];
                        item.total_votes = [];
                        item.featuredImage = null;
                        item.parameter_votes = 0;
                        // LINK FOR TOPIC DETAIL VIEW
                        item.url = '/cb/' + vm.cbKey + '/topic/' + item.topic_key + '?type=' + template;

                        // FLAG FOR WHEN IN FILTER MODE
                        item.visible = true;
                        vm.countTopics++;
                        vm.countTopicsTotal++;

                        // LINK FOR TOPIC DETAIL VIEW
                        item.url = '/cb/' + vm.cbKey + '/topic/' + item.topic_key + '?type=default';


                        // DEAL WITH CACHED VALUES FOR PARAMETERS
                        var parameters = JSON.parse(item._cached_data).parameters;


                        // DEAL WITH IMAGES
                        var images = vm._helper_getParameteresAccordingToCode(parameters, 'images');

                        // DEAL WITH FILES
                        var files = vm._helper_getParameteresAccordingToCode(parameters, 'files');

                        // BUILD IMAGES
                        item.images = vm.helper_buildAccordingToParameterValue(images);

                        // SET THE FEATURED IMAGE
                        if (item.images.length > 0) {
                            item.featuredImage = item.images[0].url + '?h=150&extension=jpeg&quality=65';
                        } else {
                            item.featuredImage = vm.defaultImage + '?h=150&extension=jpeg&quality=65';
                        }
                        // BUILD FILES
                        item.files = vm.helper_buildAccordingToParameterValue(files);

                        // DEAL WITH GEOLOCATION
                        var geolocation = vm._helper_getParameteresAccordingToCode(parameters, 'google_maps');

                        if (geolocation && geolocation[0]) { // WE HAVE A PARAMETER FOR GEOLOCATION

                            // DEAL WITH THE PARAMATER VALUE
                            var preparedValues = vm._helper_splitAndConvertItemsToGivenFormat(geolocation[0].pivot.value, ',', 'float');

                            // WE OBTAINED A VALID PREPARED VALUE
                            if (preparedValues) {

                                // BUILD THIS TOPIC GEOLOCATION
                                vm.geolocation = {'lat': preparedValues[0], 'long': preparedValues[1]};

                                // NOTIFY MAP'S COMPONENT
                                Event.$emit('loadedLocation', vm.geolocation);
                            }
                        }
                        // DEAL WITH PARAMETERS WE WANT TO LIST IN THE VIEW
                        parameters = parameters.filter(function (parameter) {
                            if (parameter.pivot) {
                                return parameter.pivot.value !== null
                                    && parameter.visible
                                    //                                    && parameter.visible_in_list
                                    //&& !parameter.private
                                    && !vm._helper_inArray(parameter.code, vm.forbidden, true);
                            }
                        });


                        //BUILD VISIBLE PARAMETERS
                        parameters.map(function (parameter) {
                            // console.log(parameter.parameter_code);
                            // console.log(parameter.pivot);
                            // TITLE ACCORDING TO THE CURRENT LANGUAGE
                            parameter.title = parameter.translations[vm.currentLanguage].parameter;
                            // DEAL WITH PARAMETERS WE WANT TO LIST IN THE VIEW
                            parameters = parameters.filter(function (parameter) {
                                if (parameter.pivot) {
                                    return parameter.pivot.value !== null
                                        && parameter.visible
//                                    && parameter.visible_in_list
                                        //&& !parameter.private
                                        && !vm._helper_inArray(parameter.code, vm.forbidden, true);
                                }
                            });


                            //BUILD VISIBLE PARAMETERS
                            parameters.map(function (parameter) {
                                // console.log(parameter.parameter_code);
                                // console.log(parameter.pivot);
                                // TITLE ACCORDING TO THE CURRENT LANGUAGE
                                parameter.title = parameter.translations[vm.currentLanguage].parameter;

                                // HAS OPTIONS
                                if (parameter.type.options) {

                                    // BUILD THE OPTIONS TO VALIDATE
                                    var selectedOptions = vm._helper_splitAndConvertItemsToGivenFormat(parameter.pivot.value, ',', 'integer');

                                    // FECTH ONLY THE SELECTED OPTIONS
                                    var options = parameter.options.filter(function (item) {
                                        if (vm._helper_inArray(item.id, selectedOptions)) {
                                            return item;
                                        }
                                    });

                                    // FECTH ONLY THE SELECTED OPTIONS
                                    var options = parameter.options.filter(function (item) {
                                        if (vm._helper_inArray(item.id, selectedOptions)) {
                                            return item;
                                        }
                                    });
                                    // BUILD THE OPTIONS ACCORDING TO THE CURRENT LANGUAGE
                                    options.map(function (option) {
                                        option.title = option.translations[vm.currentLanguage].label;
                                        if (item.featuredImage === vm.defaultImage + '?h=150&extension=jpeg&quality=65') {
                                            if (vm.template === 'project' && parameter.parameter_code === 'tematic_area' && option.code) {
                                                item.featuredImage = option.code + '?h=150&extension=jpeg&quality=65';
                                            }
                                        }
                                    });

                                    // NEW VARIABLE TO MAKE EASIER TO DEAL WITH IN THE VIEW
                                    parameter._options = options;


                                } else {
                                    parameter.value = parameter.pivot.value;
                                }
                                if (parameter.parameter_code == 'address') {
                                    parameter.address = parameter.pivot.value;
                                    item.address = parameter.address;
                                }
                                if (parameter.parameter_code == 'votes') {
                                    item.parameter_votes = parameter.pivot.value;
                                }

                                if (parameter.parameter_code == 'participants') {
                                    parameter.participants = parameter.pivot.value;
                                    item.participants = parameter.participants;
                                }
                                if (parameter.parameter_code == 'date') {
                                    parameter.date = parameter.pivot.value;
                                    var newDate = parameter.date.split("-");
                                    var myDate = new Date(newDate[0], newDate[1] - 1, newDate[2]);
                                    item.monthName = monthNames[newDate[1] - 1];
                                    item.day = newDate[2];
                                    item.month = newDate[1] - 1;
                                    item.myDate = myDate;
                                    item.sortDate = parameter.pivot.value;
                                }
                                if (parameter.parameter_code == 'hour') {
                                    parameter.hour = parameter.pivot.value;
                                    item.hour = parameter.hour;
                                }
                                if (parameter.parameter_code == 'city') {
                                    parameter.city = parameter.pivot.value;
                                    item.city = parameter.city;
                                }
                                if (parameter.parameter_code == 'contacts') {
                                    parameter.contacts = parameter.pivot.value;
                                    item.contacts = parameter.contacts;
                                }
                            });

                            // SET THE PARAMETERS
                            item.parameters = parameters;

                            //SET THE STATUS OF THE TOPIC
                            var status = item.status;
                            status = status.filter(function (state) {
                                return state.active;
                            });

                            status.map(function (state) {
                                var statusTypeTranslations = state.status_type.status_type_translations;
                                statusTypeTranslations.map(function (s) {
                                    if (s.language_code === vm.currentLanguage) {
                                        state.name = s.name;
                                    }
                                });
                            });
                        });
                    })

                    if (vm.template == "event") {
                        topics.map(function (item) {
                            item.newId = '#' + item.id;
                        });
                        topics.sort(function (a, b) {
                            return a.hour > b.hour;
                        });
                        topics.sort(function (a, b) {
                            return a.sortDate > b.sortDate;
                        });
                        var newTopics = {};
                        for (var i = 0; i < topics.length; i++) {
                            if(topics[i].day && topics[i].month) {
                                if (!newTopics[topics[i].day + '_' + topics[i].month]) {
                                    newTopics[topics[i].day + '_' + topics[i].month] = [];
                                    newTopics[topics[i].day + '_' + topics[i].month].day = topics[i].day;
                                    newTopics[topics[i].day + '_' + topics[i].month].monthName = topics[i].monthName;
                                }
                                newTopics[topics[i].day + '_' + topics[i].month].push(topics[i]);
                            }
                        }
                        console.log(newTopics);
                        topics = newTopics;
                        console.log(topics);


                    }

                    // SET THE TOPICS LIST
                    vm.topics = topics;

                    // REMOVE LOADER FROM VIEW
                    vm.loading_topics = false;
                    vm.showStatistics = true;

                    // LOAD AVAILABLE ACTIONS
                    vm.loadAvailableActions();
                    // THIS SHOULD BE A PROP
                    if(vm.template === 'project'){
                        vm.filterByStatus('approved')
                    }
                }).catch(function (error) {
                console.log(error)
                //DEAL WITH ERRORS
            });
        },

        // LOAD VOTES FOR THIS PAD
        loadVotes: function () {
            var vm = this;
            // FETCH VOTES BY AJAX CALL
            axios.post('/cb/getPadVotes', {
                'voteKeys': vm.voteKeys

            }).then(function (response) {

                // SET THE VOTES
                vm.votes = response.data;

                // BUILD THE VOTES
                vm.votes.map(function (vote){

                    // INITIAL VALUES
                    vote.total_votes = 0;
                    vote.total_voters = 0;
                    vote.allowed = false;

                    //vote.is_open = vote.is_open;

                    vote.vote_key = vote.event_key;

                    // VOTES FROM THE CURRENT LOGGED USER IN THIS VOTE EVENT
                    vote.user_registered_votes = vote.user_votes.length;

                    // CHECK IF IS SET MAX VOTES CONFIGURATION FOR THIS EVENT
                    var total_votes_allowed = vm._helper_getParameteresAccordingToCode(vote.configurations,'total_votes_allowed');


                    if(total_votes_allowed && total_votes_allowed[0]){
                        // SUBTRACT THE ALREADY USED VOTES
                        vote.total_votes_allowed = total_votes_allowed[0].value - vote.user_votes.length;
                    }

                    // GET THIS VOTE FROM THE PAD TO ACCESS THE CONFIGURATIONS
                    var voteKey = vm.voteKeys.filter(function(voteItem){
                        return voteItem.vote_key === vote.vote_key;
                    });

                    // CHECK IF VOTE REQUIRES SUBMISSION
                    var requiresSubmision = vm._helper_getParameteresAccordingToCode(voteKey[0].vote_configurations,'boolean_requires_confirm');
                    var needsToConfirm = vm._helper_getParameteresAccordingToCode(voteKey[0].vote_configurations,'boolean_show_confirmation_view');
                    // IF REQUIRES SUBMISSION
                    if(requiresSubmision[0]){
                        if(requiresSubmision[0].pivot.value == 1){
                            vote.needs_submission = true;
                            vote.user_has_submitted = vote.already_submitted;

                            // SUBMITTED DATE IS DEFINED
                            if(vote.submited_date){
                                vote.user_submitted_at = vote.submited_date;
                            }

                            // IF USER HAS NOT SUBMITTED PREPARE URL
                            if(!vote.user_has_submitted){
                                vote.submission_link = '/'; // REPLACE WITH CORRECT URL

                                // IF THE USER NEEDS TO CONFIRM THE VOTES REPLACE URL
                                if(needsToConfirm[0]){
                                    if(needsToConfirm[0].pivot.value == 1){
                                        vote.submission_link = '/cb/'+ vm.cbKey +'/showTopicsVoted'; // REPLACE WITH CORRECT URL
                                    }
                                }
                            }
                        }
                    }


                    // VOTE STATISTICS
                    var vote_counts = JSON.parse(vote._count_votes);
                    if(vote_counts && vote_counts.count){
                        vote.total_votes = vote_counts.count.total;
                        vote.total_voters = vote_counts.count.total_users;
                    }

                    // VALIDATE WHAT THE USER CAN DO
                    if(vm.actions){
                        // DO WE HAVE LOGIN LEVELS ATTACHED TO VOTE EVENTS
                        if(vm.actions.vote){
                            // WE HAVE LOGIN LEVELS ATTACHED TO THIS VOTE EVENT
                            if(vm.actions.vote[vote.vote_key]){
                                if(vm.actions.vote[vote.vote_key].allowed){ // THE USER IS ALLOWED
                                    vote.allowed = true;
                                }else{
                                    vote.allowed = false; // THE USER IS NOT ALLOWED TO VOTE
                                    vote.reasons = vm.actions.vote[vote.vote_key].missingAttributesPerLevel; // REASONS
                                }
                            }else{
                                vote.allowed = true; // THE USER IS ALLOWED
                            }
                        }else{
                            vote.allowed = true; // THE USER IS ALLOWED
                        }
                    }else{
                        vote.allowed = true; // THE USER IS ALLOWED
                    }


                    // ATTACH EACH VOTE EVENT TO EACH TOPIC
                    vm.topics.map(function(topic){

                        // FETCH THE CACHED VALUES FOR VOTES
                        var topicVotes = JSON.parse(topic._cached_votes);

                        // BOOLEAN USER [VOTED / VOTE]
                        topic.has_voted[vote.vote_key] = false;
                        topic.vote_value[vote.vote_key] = 1;

                        // IF THE USER HAS VOTED IN THIS TOPIC SET TO TRUE
                        if(vm._helper_inArray(topic.topic_key, vote.user_votes)) {
                            topic.has_voted[vote.vote_key] = true;
                        }

                        // TOTAL VOTES IN THIS TOPIC FOR EACH EVENT
                        topic.total_votes[vote.vote_key] = 0;

                        // Remove html from topics list
                        topic.contents = topic.contents.replace(/<(?:.|\n)*?>/gm, '');

                        if(topicVotes){
                            if(topicVotes[vote.vote_key]){ // IS THIS EVENT DEFINED

                                // ONLY DEALING WITH POSITIVE VOTES
                                topic.total_votes[vote.vote_key] = topicVotes[vote.vote_key].sum_positive;
                            }

                        }

                        // STORE THE VOTE INFORMATION IN THE TOPIC
                        topic.vote_events.push(vote);
                    });
                });
            }).catch(function (error) {
                console.log(error);
            });
        }
    },

    // FIRST CALL AFTER PAGE LOADS
    mounted() {
        var vm = this;
        // FETCH THE PAD BY AJAX CALL
        axios.get('/cb/' + this.cbKey + '/basicInformation')
            .then(function (response) {
                vm.cbType = response.data.template;
                vm.title = response.data.title;
                vm.contents = response.data.contents;
                vm.template = response.data.template;
                vm.page_key = response.data.page_key;

                var configurations = response.data.configurations;
                if(configurations) {
                    configurations.map(function (configuration) {
                        configuration.code = configuration.code;
                    });
                }

                // BUILD THE PARAMETER FILTERS
                var parameters = JSON.parse(response.data._cached_data);

                if(parameters){
                    parameters.map(function (parameter) {
                        // TITLE ACCORDING TO THE CURRENT LANGUAGE
                        parameter.title = parameter.translations[vm.currentLanguage].parameter;

                        // FILTER OPTIONS
                        if (parameter.type.options && !parameter.private && parameter.visible) { // DEAL WITH PAD RESTRICTIONS

                            // BUILD THE OPTIONS
                            parameter.options.map(function (option) {
                                // TITLE ACCORDING TO THE CURRENT LANGUAGE
                                option.title = option.translations[vm.currentLanguage].label;
                                // FLAG FOR SELECTED FILTER
                                option.selected = false;
                            });

                            // NEW VARIABLE TO MAKE EASIER TO DEAL WITH IN THE VIEW
                            parameter._options = parameter.options;
                        }
                    });
                }

                //SET THE OPERATION SCHEDULES

                vm.operation_schedules = response.data.operation_schedules;
                // SET THE CB CONFIGURATIONS
                if(configurations) {
                    vm.configurations = configurations;
                }
                // SET THE PARAMETERS
                vm.parameters = parameters;
                // SET THE VOTE KEYS
                vm.voteKeys = response.data.votes;

                if(vm.loggedUser !== 'null'){
                    vm.current_user = JSON.parse(vm.loggedUser);
                    vm.confirmed_email = vm.current_user.confirmed;
                }
                console.log(vm.cbLayout);

                // TRIGGER LOADING OF TOPICS
                vm.loadTopics();


            }).catch(function (error) {
            //DEAL WITH ERRORS
        });
    }
});

new Vue({
    el: '#root',
    data: {
        showModal: false,
        showStatistics: false
    }
});

