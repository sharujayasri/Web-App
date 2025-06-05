import sys
import json
import numpy as np
from tensorflow.keras.models import load_model

# Load the trained Random Forest model (assuming it is a Keras model)
model = load_model("..// python predict.pyrandom_forest_model.h5")

# Extract input parameters from command line arguments
age = int(sys.argv[1])
sex = int(sys.argv[2])
education = int(sys.argv[3])
smoking = int(sys.argv[4])
cigsPerDay = int(sys.argv[5])
BP = int(sys.argv[6])
stroke = int(sys.argv[7])
hypertension = int(sys.argv[8])
diabetes = int(sys.argv[9])
totChol = int(sys.argv[10])
sysBP = int(sys.argv[11])
diaBP = int(sys.argv[12])
BMI = float(sys.argv[13])
heartRate = int(sys.argv[14])
glucose = int(sys.argv[15])

# Prepare the input array for prediction (ensure the order matches what the model expects)
X_new = np.array([[age, sex, education, smoking, cigsPerDay, BP, stroke, hypertension, diabetes, totChol, sysBP, diaBP, BMI, heartRate, glucose]])

# Make the prediction using the model
prediction = model.predict(X_new)

# If it's a classification problem, convert output to positive/negative
result = "Positive" if prediction[0] == 1 else "Negative"

# Return the prediction result as JSON
print(json.dumps({"result": result}))
