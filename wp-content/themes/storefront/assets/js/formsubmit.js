$( document ).ready(function() {

  //get style t-shirt.
  var style= '';
  $('#pattern').change(function() {
    style =  $('#pattern').find(":selected").val();
  });

  //get color t-shirt.
  var color= '';
  $('#color').change(function() {
    color =  $('#color').find(":selected").val();
    alert(color);
  });

  //get size t-shirt
  var size= '';
  $('#size').change(function() {
    size =  $('#size').find(":selected").val();
    alert(size);
  });




});
