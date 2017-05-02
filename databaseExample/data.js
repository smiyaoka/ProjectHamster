var Name = document.getElementById("Name");
var submit1 = document.getElementById("submit1");



function submitClick() {
    
    var firebaseRef = firebase.database().ref();
    
    var nameText = Name.value;
    
    firebaseRef.push().set(nameText);
}