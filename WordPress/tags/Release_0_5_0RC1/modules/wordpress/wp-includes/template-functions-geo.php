<?php
function get_Lat() {
    global $post;
    return $post->post_lat;
}

function get_Lon() {
    global $post;
    return $post->post_lon;
}

function print_Lat() {
    if(get_settings('use_geo_positions')) {
        if(get_Lat() > 0) {
            echo "".get_Lat()."N";
        } else {
            echo "".get_Lat()."S";
        }
    }
}

function print_Lon() {
    global $postdata;
    if(get_settings('use_geo_positions')) {
        if(get_Lon() < 0) {
            $temp = get_Lon() * -1;
            echo "".$temp."W";
        } else {
            echo "".get_Lon()."E";
        }
    }
}

function print_PopUpScript() {
    echo "
<script type='text/javascript'>
<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->
function formHandler(form) {
  var URL = form.site.options[form.site.selectedIndex].value;
  if(URL != \".\") {
    popup = window.open(URL,\"MenuPopup\");
  }
}
</script> ";
}

function print_UrlPopNav() {
    $sites = array(
                   array('http://www.acme.com/mapper/?lat='.get_Lat().'&amp;long='.get_Lon().'&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes',
                         'Acme Mapper'),
                   array('http://geourl.org/near/?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;dist=500',
                         'GeoURLs near here'),
                   array('http://www.geocaching.com/seek/nearest.aspx?origin_lat='.get_Lat().'&amp;origin_long='.get_Lon().'&amp;dist=5',
                         'Geocaches Near Nere'),
                   array('http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude='.get_Lat().'&amp;longitude='.get_Lon(),
                         'Mapquest map of this spot'),
                   array('http://www.sidebit.com/ProjectGeoURLMap.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'SideBit URL Map of this spot'),
                   array('http://confluence.org/confluence.php?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'Confluence.org near here'),
                   array('http://www.topozone.com/map.asp?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'Topozone near here'),
                   array('http://www.findu.com/cgi-bin/near.cgi?lat='.get_Lat().'&amp;lon='.get_Lon(),
                         'FindU near here'),
                   array('http://mapserver.maptech.com/api/espn/index.cfm?lat='.get_Lat().'&amp;lon='.get_Lon().'&amp;scale=100000&amp;zoom=50&amp;type=1&amp;icon=0&amp;&amp;scriptfile=http://mapserver.maptech.com/api/espn/index.cfm',
                         'Maptech near here')
                  );
    echo '<form action=""><div>
<select name="site" size="1" onchange="formHandler(this.form);" >'."\n";
    echo '<option value=".">Sites referencing '.get_Lat().' x '.get_Lon()."</option>\n";
    foreach($sites as $site) {
        echo "\t".'<option value="'.$site[0].'">'.$site[1]."</option>\n";
    }
    echo '</select></div>
</form>'."\n";
}

function longitude_invalid() {
    if (get_Lon() == null) return true;
    if (get_Lon() > 360) return true;
    if (get_Lon() < -360) return true;
}

function print_AcmeMap_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.acme.com/mapper/?lat=".get_Lat()."&amp;long=".get_Lon()."&amp;scale=11&amp;theme=Image&amp;width=3&amp;height=2&amp;dot=Yes";
}

function print_GeoURL_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://geourl.org/near/?lat=".get_Lat()."&amp;lon=".get_Lon()."&amp;dist=500";
}

function print_GeoCache_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.geocaching.com/seek/nearest.aspx?origin_lat=".get_Lat()."&amp;origin_long=".get_Lon()."&amp;dist=5";
}

function print_MapQuest_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude=".get_Lat()."&amp;longitude=".get_Lon();
}

function print_SideBit_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://www.sidebit.com/ProjectGeoURLMap.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}

function print_DegreeConfluence_Url() {
    if (!get_settings('use_geo_positions')) return;
    if (longitude_invalid()) return;
    echo "http://confluence.org/confluence.php?lat=".get_Lat()."&amp;lon=".get_Lon();
}

?>
