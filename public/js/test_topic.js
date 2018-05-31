
// NECESSARY TO ALLOW COMMUNICATION BETWEEN COMPONENTS
window.Event = new Vue();

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
                return 13
            }
        },
        markerCoordinates: [{
            latitude: 38.730863375629575,
            longitude: -9.131621718188399
        }]
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
                //vm.map.setCenter(vm.markers[i]);
                
                var marker = new google.maps.Marker({
                    position: { lat: parseFloat( vm.markers[i].lat ), lng: parseFloat( vm.markers[i].long ) },
                    map: vm.map,
                });
                vm.map.setCenter(new google.maps.LatLng(vm.markers[i].lat, vm.markers[i].long));
                
            }
        }
    },

    created(){
        vm = this;

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
//
//
// // TOPIC COMPONENT
Vue.component('topic', {
//
    template: '#topic-template',
    props: ['cbKey', 'type', 'currentLanguage','topicKey', 'loggedUser', 'defaultImage'],

    data() {
        return {
            cb: null,
            cbConfigurations: [],
            operationsSchedules: [],
            title: null,
            actions: null, // AVAILABLE ACTIONS
            voteKeys: null, // VOTE EVENT KEYS TO SEND TO VOTE
            contents: null,
            topic_key: null,
            defaultImage: {'url':'/files/292/OuJ3LMkFTomUvsCL8xbI/1?h=250','name':'default_topic_image.jpg','type':'jpg'}, // THIS NEEDS TO BE A PROP
            featuredImage: null,// FEATURED IMAGE
            createdBy: null,
            createdOnBehalf: null,
            showInformation: false,
            loading_topic: true,

            files: [],
            votes: [], // AVAILABLE VOTE EVENTS
            images: [],
            has_voted: [],
            parameters: [],
            vote_value: [],
            total_votes: [],
            // THIS LIST DEFINES THE PARAMETERS WE DON'T WANT TO SHOW AS "RAW" VALUE
            forbidden: ['images','files'],

            height: 150,
            quality: 65,
            loading:true

        };
    },
//
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
        // [HELPER FUCNTION] TO CHECK IF ARRAY CONTAINS VALUE
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
        // [HELPER FUCNTION] FETCHS PARAMETERS ACCORDING TO CODE
        _helper_getParameteresAccordingToCode: function (parameters,code) {

            return items = parameters.filter(function (item) {
                return item.parameter_code === code;
            });

        },
        // [HELPER FUCNTION] RECEIVES A ARRAY OF ENCODED FILES AND BUILDS THE LIST
        helper_buildAccordingToParameterValue: function (parameters) {
            var items = [];
            for (var i = parameters.length - 1; i >= 0; i--) {
                var value = JSON.parse(parameters[i].pivot.value);
                if(value && value.length > 0){
                    if(value.length > 0){
                        value.map(function(val){
                            var url = '/files/'+val.id+'/'+val.code;
                            var name = val.name;
                            var pieces = name.split(".");
                            var type = pieces[(pieces.length-1)];
                            items.push({'url':url,'name':name, 'type': type})
                        })
                    }else{
                        console.log(value);
                        var url = '/files/'+value[0].id+'/'+value[0].code;
                        var name = value[0].name;
                        var pieces = name.split(".");
                        var type = pieces[(pieces.length-1)];
                        items.push({'url':url,'name':name, 'type': type});
                    }
                }
            }
            return items;
        },
        _helper_buildAccordingToCode: function (parameter){
            var item = [];
            if (parameter.length > 0) {
                var value = JSON.parse(parameter[0].pivot.value);

                if (value.length > 0) {
                    console.log(value[0], value[0].name, value[0].id, value[0].code)
                    var url = '/files/' + value[0].id + '/' + value[0].code;
                    var name = value[0].name;
                    var pieces = name.split(".");
                    var type = pieces[(pieces.length - 1)];
                    item.push({ 'url': url, 'name': name, 'type': type });
                }
            }    
            return item;
        },
        _helper_getParameterCoverTopic: function(parameters, code){
            return items = parameters.filter(function(item){
                return item.parameter_code === code;
            });
        },
        // [HELPER FUCNTION] RECEIVES A STRING SPLITS IT AND CONVERTS EACH VALUE TO A GIVEN FORMAT
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

        // TRIGGER A TOPIC VOTE IN A SPECIFIC EVENT
        triggerVote: function(vote,topic,value){
            var vm = this;
            axios.post('/cb/voteInTopic', {
                'voteKey': vote.vote_key,
                'topicKey': topic,
                'value': value,
                'cbKey': vm.cbKey
            }).then(function (response) {

                // WE EXPECT A SUCESS ALWAYS [NEED TO IMPROVE THIS]
                var result = JSON.parse(response.data);

                // ERROR IN VOTE METHOD
                if(result.errorMsg){
                    throw result.errorMsg;
                }

                vm.votes.map(function (vote_item){

                    // FIND THE VOTE
                    if(vote_item.vote_key == vote.vote_key){

                        // INCREASE / DECREASE THE USER VOTES IN THIS EVENT
                        vote_item.user_registered_votes += (result.vote == 1 ? 1 : -1);

                        // CHANGE THE USER RELATION [VOTE/VOTED] TO THIS VOTE
                        vm.has_voted[vote_item.vote_key] = result.vote;

                        // INCREASE / DECREASE THE VOTES IN THIS EVENT
                        vm.total_votes[vote_item.vote_key] += (result.vote == 1 ? 1 : -1);
                    }
                });

                // FORCE THE UPDATE THE VIEW
                vm.$forceUpdate();

            }).catch(function (error) {
                //DEAL WITH ERRORS
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

        // LOAD VOTES FOR THIS PAD
        loadVotes: function () {
            var vm = this;
            // FETCH VOTES BY AJAX CALL
            axios.post('/cb/getPadVotes', {
                'voteKeys': vm.cb.votes

            }).then(function (response) {

                // SET THE VOTES
                vm.votes = response.data;

                // BUILD THE VOTES
                vm.votes.map(function (vote){

                    // INITIAL VALUES
                    vote.total_votes = 0;
                    vote.total_voters = 0;
                    vote.allowed = false;
                    vm.has_voted[vote.vote_key] = false;

                    vote.vote_key = vote.event_key;

                    // VOTES FROM THE CURRENT LOGGED USER IN THIS VOTE EVENT
                    vote.user_registered_votes = vote.user_votes.length;

                    // CHECK IF IS SET MAX VOTES CONFIGURATION FOR THIS EVENT
                    var total_votes_allowed = vm._helper_getParameteresAccordingToCode(vote.configurations,'total_votes_allowed');


                    if(total_votes_allowed && total_votes_allowed[0]){
                        // SUBTRACT THE ALREADY USED VOTES
                        vote.total_votes_allowed = total_votes_allowed[0].value - vote.user_votes.length;
                    }

                    // VOTE STATISTICS
                    var vote_counts = JSON.parse(vote._count_votes);
                    if(vote_counts && vote_counts.count){
                        vote.total_votes = vote_counts.count.total;
                        vote.total_voters = vote_counts.count.total_users;
                    }

                    // CHECK IF THE USER VOTED ON THIS TOPIC IN THIS VOTE EVENT
                    if(vm._helper_inArray(vm.topic_key, vote.user_votes)) {
                        vm.has_voted[vote.vote_key] = true;
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
                });
            }).catch(function (error) {
                console.log(error);
            });
        }
    },
//
//     // FIRST CALL AFTER PAGE LOADS
    mounted() {
        var vm = this;
        // FETCH THE TOPIC BY AJAX CALL
        axios.get('/topic/' + this.topicKey + '/basicInformation')
            .then(function (response) {
                vm.loading_topic = false;
                vm.cb = response.data.cb;
                vm.cbConfigurations = vm.cb.configurations;
                vm.operationsSchedules = vm.cb.operation_schedules;
                vm.title = response.data.title;
                vm.summary = response.data.summary;
                vm.facebook = response.data.facebook;
                vm.contents = response.data.contents;
                vm.topic_key = response.data.topic_key;
                vm.geolocation = false;
                vm.countComments = response.data._count_comments;
                vm.created_at = response.data.created_at;
                vm.template = response.data.cb.template;
                vm.topic_number = response.data.topic_number;
                vm.parameter_votes = 0;

                // DEAL WITH CACHED VALUES FOR PARAMETERS
                var parameters = JSON.parse(response.data._cached_data).parameters;

                var image = vm._helper_getParameterCoverTopic(parameters, 'default_image');
                image = vm._helper_buildAccordingToCode(image);

                //var image = vm._helper_getParameteresAccordingToCode(parameters, 'default_image');
                // DEAL WITH IMAGES
                var images = vm._helper_getParameteresAccordingToCode(parameters, 'images');

                // DEAL WITH FILES
                var files = vm._helper_getParameteresAccordingToCode(parameters, 'files');

                // BUILD IMAGES
                vm.images = vm.helper_buildAccordingToParameterValue(images);
                // SET THE FEATURED IMAGE
                if(image[0]){
                    vm.featuredImage = image[0].url + '?h=250&extension=jpeg&quality=65';
                } else {
                    if (vm.defaultImage) {
                        vm.defaultImage = JSON.parse(vm.defaultImage);
                        vm.featuredImage = vm.defaultImage + '?h=250&extension=jpeg&quality=65';
                    }    
                }
                
                // BUILD FILES
                vm.files = vm.helper_buildAccordingToParameterValue(files);

                // DEAL WITH GEOLOCATION
                var geolocation = vm._helper_getParameteresAccordingToCode(parameters, 'google_maps');

                if (geolocation && geolocation.length > 0) { // WE HAVE A PARAMETER FOR GEOLOCATION
                    // DEAL WITH THE PARAMATER VALUE
                    var preparedValues = vm._helper_splitAndConvertItemsToGivenFormat(geolocation[0].pivot.value, ',', 'float');

                    // WE OBTAINED A VALID PREPARED VALUE
                    if (preparedValues) {

                        // BUILD THIS TOPIC GEOLOCATION
                        vm.geolocation = { 'lat': preparedValues[0], 'long': preparedValues[1] };

                        // NOTIFY MAP'S COMPONENT
                        Event.$emit('loadedLocation', vm.geolocation);
                    }
                }
                // DEAL WITH PARAMETERS WE WANT TO LIST IN THE VIEW
                parameters = parameters.filter(function (item) {
                    if (item.pivot) {
                        return item.pivot.value !== null
                            && item.visible
                            && !vm._helper_inArray(item.code, vm.forbidden, true);
                    }
                });
                // BUILD VISIBLE PARAMETERS
                parameters.map(function (parameter) {
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

                        // BUILD THE OPTIONS ACCORDING TO THE CURRENT LANGUAGE
                        options.map(function (option) {
                            option.title = option.translations[vm.currentLanguage].label;
                        });

                        // NEW VARIABLE TO MAKE EASIER TO DEAL WITH IN THE VIEW
                        parameter._options = options;

                    } else {
                        parameter.value = parameter.pivot.value;
                        if (parameter.code === 'associated_topics') {
                            var jsonValue = JSON.parse(parameter.pivot.value);
                            if (jsonValue.myTopics) {
                                parameter.value = jsonValue.myTopics.length;
                            }
                        }
                        if (parameter.parameter_code == 'votes') {
                            vm.parameter_votes = parameter.pivot.value;
                        }

                    }

                });

                vm.configurations = response.data.cb.configurations;

                // SET THE PARAMETERS
                vm.parameters = parameters;

                //SET THE STATUS OF THE TOPIC
                var status = response.data.status;
                if(status && status.length>0){
                    status = status.filter(function(state){
                        return state.active;
                    });

                    status.map(function(state){
                        var statusTypeTranslations = state.status_type.status_type_translations;
                        statusTypeTranslations.map(function (s) {
                            if(s.language_code === vm.currentLanguage){
                                state.name = s.name;
                            }
                        });
                    });
                }
                vm.status = status;

                // REMOVE LOADER FROM VIEW
                vm.loading = false;

                //TOPIC CREATED BY
                vm.createdBy = response.data.created_by;

                //TOPIC CREATED ON BEHALF
                vm.createdOnBehalf = response.data.created_on_behalf;
                if(response.data.user){
                    vm.user_name = response.data.user.name;
                }else{
                    vm.user_name = '';
                }
//alert(Date.now() > new Date("2018-12-15 11:58:24"));
                var childTopics = response.data.childs;
                if(childTopics){
                    if(childTopics.length > 1){
                        childTopics.map(function(child){
                            child.url = '/cb/'+vm.cbKey+'/topic/'+child.topic_key+'?type=default';
                        });
                    }else{
                        childTopics[0].url = '/cb/'+vm.cbKey+'/topic/'+childTopics[0].topic_key+'?type=default';
                    }
                }
                var parentTopics = response.data.parent;
                if(parentTopics){
                    if(parentTopics.length > 1){
                        parentTopics.map(function(parent){
                            parent.url = '/cb/'+vm.cbKey+'/topic/'+parent.topic_key+'?type=default';
                        });
                    }else{
                        parentTopics.url = '/cb/'+vm.cbKey+'/topic/'+parentTopics.topic_key+'?type=default';
                    }
                }

                vm.canEdit = Date.now() > new Date(JSON.stringify(vm.cb.start_topic_edit));

                vm.childTopics = childTopics;
                vm.parentTopics = parentTopics;

                vm.showInformation = true;
                vm.canEdit = (Date.now() > new Date(JSON.stringify(vm.cb.start_topic_edit))) && vm.loggedUser !== 'null';
                if(vm.loggedUser !== 'null'){
                    vm.current_user = JSON.parse(vm.loggedUser);
                    vm.canEdit = vm.canEdit && vm.current_user.user_key;
                    vm.confirmed_email = vm.current_user.confirmed;
                }
                console.log(vm);

                // LOAD AVAILABLE ACTIONS
                vm.loadAvailableActions();

            }).catch(function (error) {
            //DEAL WITH ERRORS
        });
    }
});


// MAIN APP
new Vue({
    el: '#root' // NEEDS TO BE REPRESENTED IN THE VIEW

});

