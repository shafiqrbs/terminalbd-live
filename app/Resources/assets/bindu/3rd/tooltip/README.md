# interface-micro-library
A jQuery tooltip/popover/modal plugin </br>
Developed under the MIT license http://opensource.org/licenses/MIT </br>
Designed for quick creation of interface elements </br>

# Examples of installation in html after include jQuery
```html
  <link rel="stylesheet" href="css/iml-styles.css">
  
  <script type="text/javascript" src="js/iml.js"></script>
  
  $(document).ready(function(){
    $('#link1').modal("[data-modal='MyModal1']");  
    $('#link2').modal("[data-modal='MyModal2']");
  });
  
  <a href="#"
    data-interface="tooltip"
    data-position="top-right"
    data-content="<strong>This is strong tag!</strong>"
    data-shift="10"
    data-radius = "2px"
    data-trigger="click"
    data-triggeroff="click"
    data-theme="white"
  >tooltip</a> 
  <a href="#"
    data-interface="tooltip"
    data-position="top-right"
    data-content="hi this is tooltip"
    data-shift="10"
    data-theme="black"
    data-radius = "2px"
  >tooltip</a> 
  <a href="#"
    data-interface="tooltip"
    data-position="left-bottom"
    data-content="IML v1.0.0"
    data-shift="10"
    data-defineClass = "bord"
    data-radius = "0px"
  >tooltip</a> 

  <a href="#"
    data-interface = "popover"
    data-position = "top"
    data-title = "Top"
    data-theme="white"
    data-content = "<strong>This is strong tag!</strong>"
    data-shift = "10"
    data-defineClass = "bord"
    data-radius = "3px"
    data-trigger="click"
    data-triggeroff="click"
  >popover</a> 
  <a href="#"
    data-interface = "popover"
    data-position = "top"
    data-title = "Title Popover"
    data-content = "Content Popover"
    data-shift = "10"
    data-theme = "error"
    data-radius = "3px"
    data-trigger="mouseover"
    data-triggeroff="mouseleave"
  >popover</a>
  <a href="#"
    data-interface = "popover"
    data-position = "top"
    data-title = "none"
    data-content = "Content Popover 3"
    data-shift = "10"
    data-theme = "smoke"
    data-radius = "3px"
    data-trigger="click"
    data-triggeroff="click"
  >popover</a>
  
  <a href="#" id="link1" 
    data-interface="modal" 
    data-width="550px" 
    data-height="200px" 
    data-color="#000"
  >modal</a>
  <a href="#" id="link2"
    data-interface="modal" 
    data-width="450px" 
    data-height="200px" 
  >modal</a>

  <div  class="modal" data-modal="MyModal1">
    <div class="modal_title">Modal 1 title</div>
    <div class="modal_content">Modal 1 content</div>
    <div class="close">X</div>
  </div>
  <div  class="modal" data-modal="MyModal2" >
    <div class="modal_title">Modal 2 title</div>
    <div class="modal_content">Modal 2 content</div>
    <div class="close">X</div>
  </div>

```
