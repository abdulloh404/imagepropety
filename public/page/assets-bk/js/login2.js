// Sample input dialog by David Lee
// Original concept from to https://andwecode.com/create-popup-login-and-signup-form/ and zomato.com (previously urban spoon)
// Uses Parse to reset the password

$(document).ready(function() {
    $('.dialog-overlay').hide();
    $('.dialog-box').hide();
    showAddCompentecyDialog();
  });
  
  function showAddCompentecyDialog() {
    $('.dialog-overlay').fadeIn(500);
    $('.dialog-box').fadeIn(500);
  }
  
  function cancelAddCompentecyDialog() {
    $('.dialog-overlay').fadeOut(500);
    $('.dialog-box').fadeOut(500);
  }
  
  function saveAddCompentecyDialog() {
  
  }