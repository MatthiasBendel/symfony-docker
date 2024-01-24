// Select the SVG text element
const svgText = document.getElementById('inputText');

function createSVGEllipseWithText(svg) {
  var text = svgText.textContent.slice(0, -4)
  var cy = Math.random() * parseInt(svg.getAttribute('height'));
  if (text && text.includes(" ")) {
    let parts = text.split(" ");
    parts.forEach(function(part, index) {
      (svg, part, index, parts.length - index, cy);
    });
  } else  {
    createSVGEllipseText(svg, text, 0, 0, cy);
  }
  removeAndRecreateOverlappingElements(svg);
}

function removeAndRecreateOverlappingElements(svg, text) {
  let overlapping = true;
  while (overlapping) {
    let elements = svg.querySelectorAll('ellipse, text');
    let elementsArray = Array.from(elements); // Convert the NodeList to an array
    let element = svg.querySelector('#' + text);
    console.log("Searching for overlap with " + text);
    if (element != null) {
      for (let i = 0; i < elementsArray.length; i++) {
         if (doOverlap(elementsArray[i], element)) {
            if(elementsArray[i] =! element && element != null) {
             // Elements overlap
             console.log("Found overlap for " + element + ". Removing " + elementsArray[i]);
             element2.remove(); // Remove the overlapping element
           }
         } else {
         }
       }
    }
    overlapping = false
  }
}

function doOverlap(element1, element2) {
  const rect1 = element1.getBoundingClientRect();
  const rect2 = element2.getBoundingClientRect();
  return !(
    rect1.right < rect2.left ||
    rect1.left > rect2.right ||
    rect1.bottom < rect2.top ||
    rect1.top > rect2.bottom
  );
}

function createSVGEllipseText(svg, text, leftWeight, rightWeight, cy) {
  console.log("Create ellipse for: " + text);
  var svgWidth = svg.getAttribute('width');

  // Generate random rx value
  var rx = (100 * (text.length / 5)).toString();
  var ry = 40;
  var strokeWidth = 1;

  // Generate random cx and cy values within the range of the SVG dimensions
  var cx = rx + (leftWeight * Math.random() * (svgWidth - 2*rx) / parseInt((leftWeight + rightWeight)+1)) + leftWeight;

  // Create the ellipse and text elements
  var ellipse = document.createElementNS("http://www.w3.org/2000/svg", "ellipse");
  ellipse.setAttribute("cx", cx.toString());
  ellipse.setAttribute("cy", cy.toString());
  ellipse.setAttribute("rx", rx);
  ellipse.setAttribute("ry", ry.toString());
  ellipse.setAttribute("fill", "grey");
  ellipse.setAttribute("stroke", "white");
  ellipse.setAttribute("stroke-width", strokeWidth.toString());
  ellipse.setAttribute("id", text);

  var textElement = document.createElementNS("http://www.w3.org/2000/svg", "text");
  textElement.setAttribute("x", cx.toString());
  textElement.setAttribute("y", (cy + 5).toString()); // Adjust the Y position for the text
  textElement.setAttribute("text-anchor", "middle"); // Center the text horizontally
  textElement.setAttribute("font-size", "36");
  textElement.setAttribute("font-family", "{{ font }}");
  textElement.textContent = text;
  textElement.setAttribute("id", text);

  // Append the ellipse and text to the existing "svg" element
  svg.appendChild(ellipse);
  svg.appendChild(textElement);
}

// Add an event listener to the document to listen for keyboard input
var meta = false;
document.addEventListener('keydown', async () => {
  console.log("Received input: " + event.key);
  if (event.key == 'Enter' || event.key == 'ArrowUp' || event.key == 'ArrowDown' || event.key == 'ArrowLeft'
   || event.key == 'ArrowRight') {
    createSVGEllipseWithText(document.getElementById('svg'));
    svgText.textContent = " ..."
  } else if (event.key == 'Backspace') {
    svgText.textContent = svgText.textContent.slice(0, -5) + " ..."
  } else if (event.key == 'Shift') {
  } else if (event.key == 'Alt') {
  } else if (event.key == 'Shift') {
  } else if (event.key == 'Tab') {
  } else if (event.key == 'Control') {
  } else if (event.key == 'Meta') {
    meta = true;
  } else if (meta && event.key == 'v') {
      try {
        const text = await navigator.clipboard.readText();
        svgText.textContent = svgText.textContent.slice(0, -9) + text + " ...";
        console.log('Text from clipboard: ', text);
        meta = false;
      } catch (err) {
        console.error('Failed to read from clipboard: ', err);
      }
  } else {
    svgText.textContent = svgText.textContent.slice(0, -4) + event.key + " ...";
  }
});
