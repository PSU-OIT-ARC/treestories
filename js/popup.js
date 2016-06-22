
function createCarousel(img_urls, located, base_dir){
    var count = 0;
    if(img_urls != null){
        count = img_urls.length;
    }
    var html = "<div id='popup-image-container'>";
    // Case 1: 1 story, or stories with no image.
    if(count == 1){
        html += '<a href="'+base_dir+'list.php?location='+located+'&id='+img_urls[0][0]+'">';
        html += '<div id="story-link" style="background-image: url(\'';
        if(img_urls[0][1] != ''){
            html += base_dir+img_urls[0][1];
        } else {
            html += base_dir+'pictures/tree.png';
        }
        html +='\');"';
        html += ">";
        html += "<div class='carousel-caption'>";
        html += "<p>"+img_urls[0][2]+"</p>";
        html += "</div>";
        html += "</div></a>";
    } else {
        html += "<div id='carousel' class='carousel slide' data-ride='carousel'>";
        html += "<ol class='carousel-indicators'>";
        for(i=0; i<count; i++){
            html += "<li data-target='#carousel' data-slide-to=\""+i+"\" ";
            if(i==0){
                html += "class='active' ";
            }
            html += "></li>";
        }
        html += "</ol>";
        html += "<div class='carousel-inner' role='listbox'>";
        for(i=0; i<count; i++){
            html += '<div class="item';
            if(i==0){
                html += ' active';
            }
            html += '" role="listbox">';
            html += '<a href="'+base_dir+'list.php?location='+located+'&id='+img_urls[i][0]+'">';
            html += '<div id="story-link" style="background-image: url(\'';
            if(img_urls[i][1] != ''){
                html += base_dir+img_urls[i][1];
            } else {
                html += base_dir+'pictures/tree.png';
            }
            html +='\');"';
            html += ">";
            html += "<div class='carousel-caption'>";
            html += "<p>"+img_urls[i][2]+"</p>";
            html += "</div>";
            html += "</div></a></div>";
        }
        html += "</div>";
        html += '<a class="left carousel-control" href="#carousel" role="button" data-slide="prev">';
        html += '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
        html += '<span class="sr-only">Previous</span>';
        html += '</a>';
        html += '<a class="right carousel-control" href="#carousel" role="button" data-slide="next">';
        html += '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
        html += '<span class="sr-only">Next</span>';
        html += '</a>';
        html += "</div>";
    }
    html += "</div>";
    return html;
}
