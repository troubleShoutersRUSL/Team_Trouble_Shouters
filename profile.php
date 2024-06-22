<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile Form</title>

  <script>

(function() {

var width, height, largeHeader, canvas, ctx, points, target, animateHeader = true;

// Main
initHeader();
initAnimation();
addListeners();

function initHeader() {
    width = window.innerWidth;
    height = window.innerHeight;
    target = {x: width/2, y: height/2};

    largeHeader = document.getElementById('large-header');
    largeHeader.style.height = height+'px';

    canvas = document.getElementById('demo-canvas');
    canvas.width = width;
    canvas.height = height;
    ctx = canvas.getContext('2d');

    // create points
    points = [];
    for(var x = 0; x < width; x = x + width/20) {
        for(var y = 0; y < height; y = y + height/20) {
            var px = x + Math.random()*width/20;
            var py = y + Math.random()*height/20;
            var p = {x: px, originX: px, y: py, originY: py };
            points.push(p);
        }
    }

    // for each point find the 5 closest points
    for(var i = 0; i < points.length; i++) {
        var closest = [];
        var p1 = points[i];
        for(var j = 0; j < points.length; j++) {
            var p2 = points[j]
            if(!(p1 == p2)) {
                var placed = false;
                for(var k = 0; k < 5; k++) {
                    if(!placed) {
                        if(closest[k] == undefined) {
                            closest[k] = p2;
                            placed = true;
                        }
                    }
                }

                for(var k = 0; k < 5; k++) {
                    if(!placed) {
                        if(getDistance(p1, p2) < getDistance(p1, closest[k])) {
                            closest[k] = p2;
                            placed = true;
                        }
                    }
                }
            }
        }
        p1.closest = closest;
    }

    // assign a circle to each point
    for(var i in points) {
        var c = new Circle(points[i], 2+Math.random()*2, 'rgba(255,255,255,0.3)');
        points[i].circle = c;
    }
}

// Event handling
function addListeners() {
    if(!('ontouchstart' in window)) {
        window.addEventListener('mousemove', mouseMove);
    }
    window.addEventListener('scroll', scrollCheck);
    window.addEventListener('resize', resize);
}

function mouseMove(e) {
    var posx = posy = 0;
    if (e.pageX || e.pageY) {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY)    {
        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    target.x = posx;
    target.y = posy;
}

function scrollCheck() {
    if(document.body.scrollTop > height) animateHeader = false;
    else animateHeader = true;
}

function resize() {
    width = window.innerWidth;
    height = window.innerHeight;
    largeHeader.style.height = height+'px';
    canvas.width = width;
    canvas.height = height;
}

// animation
function initAnimation() {
    animate();
    for(var i in points) {
        shiftPoint(points[i]);
    }
}

function animate() {
    if(animateHeader) {
        ctx.clearRect(0,0,width,height);
        for(var i in points) {
            // detect points in range
            if(Math.abs(getDistance(target, points[i])) < 4000) {
                points[i].active = 0.3;
                points[i].circle.active = 0.6;
            } else if(Math.abs(getDistance(target, points[i])) < 20000) {
                points[i].active = 0.1;
                points[i].circle.active = 0.3;
            } else if(Math.abs(getDistance(target, points[i])) < 40000) {
                points[i].active = 0.02;
                points[i].circle.active = 0.1;
            } else {
                points[i].active = 0;
                points[i].circle.active = 0;
            }

            drawLines(points[i]);
            points[i].circle.draw();
        }
    }
    requestAnimationFrame(animate);
}

function shiftPoint(p) {
    TweenLite.to(p, 1+1*Math.random(), {x:p.originX-50+Math.random()*100,
        y: p.originY-50+Math.random()*100, ease:Circ.easeInOut,
        onComplete: function() {
            shiftPoint(p);
        }});
}

// Canvas manipulation
function drawLines(p) {
    if(!p.active) return;
    for(var i in p.closest) {
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.closest[i].x, p.closest[i].y);
        ctx.strokeStyle = 'rgba(156,217,249,'+ p.active+')';
        ctx.stroke();
    }
}

function Circle(pos,rad,color) {
    var _this = this;

    // constructor
    (function() {
        _this.pos = pos || null;
        _this.radius = rad || null;
        _this.color = color || null;
    })();

    this.draw = function() {
        if(!_this.active) return;
        ctx.beginPath();
        ctx.arc(_this.pos.x, _this.pos.y, _this.radius, 0, 2 * Math.PI, false);
        ctx.fillStyle = 'rgba(156,217,249,'+ _this.active+')';
        ctx.fill();
    };
}

// Util
function getDistance(p1, p2) {
    return Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2);
}

})();
  </script>
  <style>
    body {
    font-family: 'Arial', 'Helvetica', sans-serif;
    background-color:  paleturquoise;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: #343a40;     
}

.form-container {
    margin: 0 20px;
}

form {
    background-color: pink;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    max-width: 800px;
    box-sizing: border-box;
    margin-top: 20px;
    display: flex;
    gap: 20px;
    transition: all 0.3s ease-in-out;
}

.column-left,
.column-right {
    flex: 1;
}

label {
    display: block;
    margin-bottom: 8px;
    color: purple;
    font-weight: 600;
}

input,
select {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    box-sizing: border-box;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s ease-in-out;
}

input:focus,
select:focus {
    border-color: #80bdff;
    outline: none;
}

button {
    background-color: purple;
    color: #fff;
    padding: 14px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    transition: background-color 0.3s ease-in-out;
}

button:hover {
    background-color: #0056b3;
}

img {
    max-width: 100%;
    height: auto;
    margin-bottom: 20px;
    border-radius: 5px;
}

@media (max-width: 768px) {
    form {
        flex-direction: column;
        padding: 20px;
    }

    .column-left,
    .column-right {
        padding: 50px;
    }
}


/* Header */
.large-header {
	position: relative;
	width: 100%;
	background: #333;
	overflow: hidden;
	background-size: cover;
	background-position: center center;
	z-index: 1;
}

#large-header {
	background-image: url('https://www.marcoguglie.it/Codepen/AnimatedHeaderBg/demo-1/img/demo-1-bg.jpg');
}

.main-title {
	position: absolute;
	margin: 0;
	padding: 0;
	color: #f9f1e9;
	text-align: center;
	top: 50%;
	left: 50%;
	-webkit-transform: translate3d(-50%,-50%,0);
	transform: translate3d(-50%,-50%,0);
}

.demo-1 .main-title {
	text-transform: uppercase;
	font-size: 4.2em;
	letter-spacing: 0.1em;
}

.main-title .thin {
	font-weight: 200;
}

@media only screen and (max-width : 768px) {
	.demo-1 .main-title {
		font-size: 3em;
	}
}



  </style>
</head>
<body>

<div id="large-header" class="large-header">
  <canvas id="demo-canvas"></canvas>
    <h1 class="main-title">Connect <span class="thin">Three</span></h1>
</div>

  <div class="form-container">
    <form action="process_form.php" method="POST" enctype="multipart/form-data" id="profileForm">
      <div class="column-left">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="university">University:</label>
        <select name="university" id="university" required>
          <option value="" disabled selected>Select your university</option>
          <option value="University of Colombo">University of Colombo</option>
          <option value="Eastern University">Eastern University</option>
          <option value="University of Jaffna">University of Jaffna</option>
          <option value="University of Kelaniya">University of Kelaniya</option>
          <option value="University of Moratuwa">University of Moratuwa</option>
          <option value="Open University, Nawala">Open University, Nawala</option>
          <option value="University of Peradeniya">University of Peradeniya</option>
          <option value="Rajarata University">Rajarata University</option>
          <option value="University of Ruhuna">University of Ruhuna</option>
          <option value="Sabaragamuwa University">Sabaragamuwa University</option>
          <option value="South Eastern University">South Eastern University</option>
          <option value="University of Sri Jayewardenepura">University of Sri Jayewardenepura</option>
          <option value="Uva Wellassa University">Uva Wellassa University</option>
          <option value="University of the Visual and Performing Arts">University of the Visual and Performing Arts</option>
          <option value="Wayamba University">Wayamba University</option>
          <option value="Gampaha Wickramarachchi University">Gampaha Wickramarachchi University</option>
          <option value="University of Vavuniya">University of Vavuniya</option>
        </select>

        <label for="faculty">Faculty:</label>
        <input type="text" name="faculty" id="faculty" placeholder="Enter faculty name" required>
        
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required>

        <label for="mobileNumber">Mobile Number:</label>
        <input type="tel" name="mobileNumber" id="mobileNumber" placeholder="Enter mobile number" required minlength="10" maxlength="10" pattern="\d{10}" oninput="this.value=this.value.slice(0,10)">
       
        <label for="gpa">GPA:</label>
        <input type="number" name="gpa" id="gpa" placeholder="Enter your GPA" required min="0" max="4" step="0.01" pattern="^\d+(\.\d{1,2})?$">

        <label>Extracurricular Activity</label>
        <textarea id="activities" name="activities" rows="4" cols="50" placeholder="type extracurricular activities"></textarea>
 
        <label>Home Number</label>
        <input type="text" name="homeNumber" placeholder="Enter Home No" required>
        
        <label>Street Address</label>
        <input type="text" name="streetAddress" placeholder="Enter Street Address" required>
                        
        <label>City</label>
        <input type="text" name="city" placeholder="Enter City" required>

        <label>District Name</label>
        <input type="text" name="districtName" placeholder="Enter District Name" required>
                  
        <button type="submit">Submit</button>
      </div>

      

  <script>
    function displayProfilePic() {
      const input = document.getElementById('photo');
      const img = document.getElementById('profilePic');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
          img.src = e.target.result;
          img.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>
</html>
