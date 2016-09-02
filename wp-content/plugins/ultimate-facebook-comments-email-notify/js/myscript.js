var ultimate_fb_table = document.getElementById('the-comment-list');
var ultimate_fb_tr = ultimate_fb_table.rows;
var inc_i = 0;
while(inc_i < (ultimate_fb_table.rows.length-1)) {
try { var ultimate_fb_imgtd = ultimate_fb_tr[inc_i].cells[1];
var ultimate_fb_imga = ultimate_fb_imgtd.getElementsByTagName('A')[0].href;
var ultimate_fb_imgaid = ultimate_fb_imga.split('?');
if(ultimate_fb_imgaid[0] == 'http://facebook.com/profile.php') {
var ultimate_fb_imgaidvalue = ultimate_fb_imgaid[1].split('=');
var ultimate_fb_img_url = ultimate_fb_imgaidvalue[1];
ultimate_fb_imgtd.getElementsByTagName('IMG')[0].src='https://graph.facebook.com/'+ultimate_fb_img_url+'/picture';
}} catch(err) {}
inc_i++;
}