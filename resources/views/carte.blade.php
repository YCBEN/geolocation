<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <!-- Nous chargeons les fichiers CDN de Leaflet. Le CSS AVANT le JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin="" />
        
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"/>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.76.1/dist/L.Control.Locate.min.css" />
     
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        
        <style type="text/css">
            #map{ /* la carte DOIT avoir une hauteur sinon elle n'apparaît pas */
                height:850px;
                width:1450px;

            }
            table, td {
            border: 1px solid black;
                }
        </style>
        <title>Carte</title>
    </head>

<body>
        <div id="map">
	    <!-- Ici s'affichera la carte -->
	    </div>

        <!-- Fichiers Javascript -->
        
        <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.76.1/dist/L.Control.Locate.min.js" charset="utf-8"></script>
       
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

       
    
       <script type="text/javascript">
           
            var lat =36.773050;
            var lon =3.061009;
            var macarte = null;
            var newloc;

            var all_delivers_url = "/api/geolocation";
            
           

            // Fonction d'initialisation de la carte
            function initMap() {
                // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
                macarte = L.map('map').setView([lat, lon], 100);
                // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
                L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {

                    // Il est toujours bien de laisser le lien vers la source des données
                    attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
                    minZoom: 1,
                    maxZoom: 20
                }).addTo(macarte);
                var arrayMarkers = [];
                var markers = L.markerClusterGroup();
                setInterval(() => {
                                        
                   
                    
                    if(markers){
                       
               
                    macarte.removeLayer(markers);
                       
                    markers=null;
                    markers = L.markerClusterGroup();
                    arrayMarkers = [];
                    }
                   
                    
                   
                        $.get(all_delivers_url, function(villes, status){
                    console.log(villes);

                   for(ville in villes)   {
                            
                            var marker = L.marker([villes[ville].lat, villes[ville].lon])//.addTo(macarte);



                            marker.bindPopup("<p>Latitude: "+ villes[ville].lat +"</p></br><p>Longitude: "+villes[ville].lon);
                            
                           
                            markers.addLayer(marker);
                    // on ajoute les marqueur  au tableau pour l utiliser au zoom  

                            arrayMarkers.push(marker); 
                        }                       
                });
                
   
           
                // on regroupe les marqueurs dans un groupe de leaflet  
                var groupe = new L.featureGroup(arrayMarkers);

                // on adapte le zoom au groupe
                
                try{
                    macarte.fitBounds(groupe.getBounds());
                }catch (error){
                    console.log('unsupported bounds')
                }

                macarte.addLayer(markers);
              
            }, 2000);
          
                // l echel de la map
               L.control.scale({
                metric: true,
                imperial:false,
                position: 'topright'
               }).addTo(macarte);


                // un marqueur partout
               macarte.on('click', function(e) {
                    if(newloc != null){

                        macarte.removeLayer(newloc);
                        newloc  = L.marker([e.latlng.lat, e.latlng.lng]);
                    
                    macarte.addLayer(newloc);
                    newloc.bindPopup("<p>Latitude: "+e.latlng.lat+"</p></br><p>Longitude: "+e.latlng.lng+"</p>").openPopup();
                    document.getElementById('lat').value=''+e.latlng.lat;
                    document.getElementById('lon').value=''+e.latlng.lng;

                    }else{
                        newloc = L.marker([e.latlng.lat, e.latlng.lng]);
                    
                        macarte.addLayer(newloc);
                        newloc.bindPopup("<p>Latitude: "+e.latlng.lat+"</p></br><p>Longitude: "+e.latlng.lng+"</p>").openPopup();
                        document.getElementById('lat').value=''+e.latlng.lat;
                        document.getElementById('lon').value=''+e.latlng.lng;
                    }
                  
                  
                });

              

                var lc= L.control.locate().addTo(macarte);
                // request location update and set location
               lc.start();
               
            }


                        
            window.onload = function(){
		    // Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
		        initMap(); 
            };
        </script>
@auth
<label id="name">{{ Auth::user()->name }}</label>

@if ( !Auth::user()->geolocation)
    <form action="http://127.0.0.1:8000/api/geolocation" method="POST">
        @csrf
        <label for="latitude">Latitude</label>
        <input id="lat" type="text" name="latitude" value="" /><br/>
    
        <label for="longitude">Longitude</label>
        <input id="lon" type="text" name="longitude" value="" /><br/>
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" /><br/>
    <input type="submit" value="Ajouter"/>
@else
    <form action="http://127.0.0.1:8000/api/geolocation/{{ Auth::user()->id }}" method="POST">
    @method('PUT')
    
    <label for="latitude">Latitude</label>
    <input id="lat" type="text" name="latitude" value="{{Auth::user()->geolocation->lon}}" /><br/>

    <label for="longitude">Longitude</label>
    <input id="lon" type="text" name="longitude" value="{{Auth::user()->geolocation->lon}}" /><br/>
   
    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" /><br/>
    <input type="submit" value="Modifier"/>
@endif
    

  </form>

  <table>
    <thead>
        <th>
            id
        </th> <th>
            email
        </th>
        <th>
            latitude
        </th>
        
        <th>
            Longitude
        </th>
        <th>
           Updated at
        </th>
    </thead>
         @foreach ($users as $user)
        <tr onclick="clickTr({{ $user->geolocation->lat}},{{$user->geolocation->lon}})">
            <td> {{ $user->id }}</td>
            <td> {{ $user->email}}</td>
            @if ($user->geolocation)
                <td> 
                    {{ $user->geolocation->lat}}
                </td>    
                <td>
                    {{ $user->geolocation->lon}}
                </td>    
                <td>
                    {{ $user->geolocation->updated_at}}
                </td> 
            @endif
        </tr>    
    @endforeach
    <tr>

    </tr>
    
  </table>
@endauth 



</body>
</html>

<script>

    function fetch(){
        $.get("http://127.0.0.1:8000/api/geolocation", function(data, status){
            alert("Data: " + data + "\nStatus: " + status);
        });
    }




    function clickTr( lat, lon ){
        alert(lat + lon);
    }
</script>

