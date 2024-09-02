import os
import cv2
import numpy as np

def main():

    path1 = "uploads/template.jpg"
    path2 = "uploads/image.jpg"

    # Check if the paths exist
    if os.path.exists(path1):
        print(f"Path 1 exists: {path1}")
    else:
        print(f"Path 1 does not exist: {path1}")

    if os.path.exists(path2):
        print(f"Path 2 exists: {path2}")
    else:
        print(f"Path 2 does not exist: {path2}")


    # Load the source image and template image
    template_image = cv2.imread(path1)
    source_image = cv2.imread(path2)

    # Convert images to grayscale
    gray_source = cv2.cvtColor(source_image, cv2.COLOR_BGR2GRAY)
    gray_template = cv2.cvtColor(template_image, cv2.COLOR_BGR2GRAY)

    # Get the width and height of the template
    w, h = gray_template.shape[::-1]

    # Perform template matching using cv2.matchTemplate
    res = cv2.matchTemplate(gray_source, gray_template, cv2.TM_CCOEFF_NORMED)

    # Set a threshold for matching
    threshold = 0.6
    loc = np.where(res >= threshold)

    # Draw rectangles around matched regions
    for pt in zip(*loc[::-1]):
        cv2.rectangle(source_image, pt, (pt[0] + w, pt[1] + h), (0, 255, 0), 2)

    # Save the result
    cv2.imwrite("results/result.jpg", source_image)


if __name__ == "__main__":
    main()
