const svg = document.querySelector('svg');
let activeEllipse = null;
let offset = { x: 0, y: 0 };

//let svg2 = generateCircleOfEllipses(370, 250, 200, 16);
//let svg3 = generateCircleOfEllipses(370, 250, 140, 16);
//document.getElementById('svg').innerHTML += svg2;
//document.getElementById('svg').innerHTML += svg3;

function handleMouseDown(e) {
  activeEllipse = e.target;
  const bbox = activeEllipse.getBBox();
  offset = {
    x: e.clientX - bbox.x,
    y: e.clientY - bbox.y
  };
  activeText = document.getElementById(e.target.id);
  const elements = document.getElementsByClassName('1_Hello');

  console.log(activeText);
}

function handleMouseMove(e) {
  if (activeEllipse) {
    const bbox = activeEllipse.getBBox();
    activeEllipse.setAttribute('cx', e.clientX - offset.x);
    activeEllipse.setAttribute('cy', e.clientY - offset.y);
    activeText.setAttribute('cx', e.clientX - offset.x);
    activeText.setAttribute('cy', e.clientY - offset.y);

    checkForOverlap(activeEllipse)
  }
}

function handleMouseUp() {
  if (activeEllipse) {
    checkForOverlap(activeEllipse)
    save(activeEllipse);
    activeEllipse = null;
  }
}

function checkForOverlap(activeEllipse) {
    // Check for overlap with other ellipses
    ellipses.forEach(ellipse => {
      if (ellipse !== activeEllipse) {
        const rect1 = activeEllipse.getBoundingClientRect();
        const rect2 = ellipse.getBoundingClientRect();
        if (!(rect1.right < rect2.left ||
              rect1.left > rect2.right ||
              rect1.bottom < rect2.top ||
              rect1.top > rect2.bottom)) {
           console.log("found overlap");
           console.log(ellipse);
          // If there is an overlap, replace the active ellipse
          ellipse.setAttribute('cx', Number(ellipse.getAttribute('cx')) + 10);
          ellipse.setAttribute('cy', Number(ellipse.getAttribute('cy')) + 10);

           console.log(ellipse);
          //checkForOverlap(activeEllipse);
          //checkForOverlap(ellipse);
        }
      }
    });
}


const texts = document.querySelectorAll('text');
const ellipses = document.querySelectorAll('ellipse');
ellipses.forEach(ellipse => {
  ellipse.addEventListener('mousedown', handleMouseDown);
});

document.addEventListener('mousemove', handleMouseMove);
document.addEventListener('mouseup', handleMouseUp);

// Function to handle the click event on the ellipses
function handleEllipseClick(e) {
  const colorPicker = document.createElement('input');
  colorPicker.setAttribute('type', 'color');
  colorPicker.value = e.target.getAttribute('fill');
  colorPicker.addEventListener('input', function() {
    e.target.setAttribute('fill', colorPicker.value);
  });
  colorPicker.click();
}

// Add event listeners to each ellipse to handle the click event
ellipses.forEach(ellipse => {
  ellipse.addEventListener('click', handleEllipseClick);
});




function generateCircleOfEllipses(centerX, centerY, radius, numEllipses) {
  let svgCode2 = '<svg width="500" height="500">';
  for (let i = 0; i < numEllipses; i++) {
    let angle = (i / numEllipses) * 2 * Math.PI;
    let x = centerX + radius * -1 * Math.sin(angle);
    let y = centerY + radius * -1 * Math.cos(angle);
    let ellipse = `<ellipse cx="${x}" cy="${y}" rx="20" ry="10" fill="blue" />`;
    svgCode2 += ellipse;
    radius = radius - 5;
  }
  svgCode2 += '</svg>';
  return svgCode2;
}

// Make an AJAX request to the Symfony controller
function save(ellipse) {
  // Create a JSON object
  var jsonData = {
    x: Number(ellipse.getAttribute('cx')),
    y: Number(ellipse.getAttribute('cy'))
  };

  console.log(JSON.stringify(jsonData));
  // Make a POST request to the Symfony controller
  fetch('/save', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(jsonData),
  })
  .then(response => response.json())
  .then(data => {
    console.log('Success:', data);
  })
  .catch((error) => {
    console.error('Error:', error);
  });

}
