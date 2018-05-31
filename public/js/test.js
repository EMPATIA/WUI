// NECESSARY TO ALLOW COMMUNICATION BETWEEN COMPONENTS
window.Event = new Vue();

// MAP COMPONENT [COMUNICATES WITH THE TOPIC COMPONENT]
Vue.component('google-map', {

    template:'<div id="google-map" class="gmap"></div>', // THIS IS THE ONLY HTML NECESSARY

    props: { // PROPS NEED TO BE SENT FROM THE VIEW [e.g. TOPIC COMPONENT]
        'latitude': {
            type: Number,
            default: function(){
                return 38.7
            }
        },
        'longitude': {
            type: Number,
            default: function(){
                return -9.13
            }
        },
        'zoom': {
            type: Number,
            default: function(){
                return 10
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
            alert(1);
            vm = this;
            for (var i = 0; i < vm.markers.length; i++) {
                var marker = new google.maps.Marker({
                    position: { lat: parseFloat( 38.730863375629575 ), lng: parseFloat( -9.131621718188399 ) },
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

        new google.maps.Marker({
            position: { lat: parseFloat( 38.730863375629575 ), lng: parseFloat( -9.131621718188399 ) },
            map: vm.map,
        });
    }

});

new Vue({
    el: '#root' // NEEDS TO BE REPRESENTED IN THE VIEW

});