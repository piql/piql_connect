import sys
import re
import uuid

# If any of the required command line arguments are missing, display a semi-helpful 
# text and then halt execution
usage = "Usage: python3 prepare_realm.py <input file> <output file> <realm name> <realm display name (in quotes)>"
if len(sys.argv) != 5:
    print(usage)
    quit()

# Store the commandline arguments in variables
inputFileName = sys.argv[1]
outputFileName = sys.argv[2]
realmName = sys.argv[3]
realmDisplayName = sys.argv[4]

# Open the input file file, read the contents, and close the file again.
inputFile = open(inputFileName, 'r')
inputText = inputFile.read()
inputFile.close()

# Parse the template text for UUIDs, and store them in a list
UUIDs = re.compile(r'[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}', re.IGNORECASE).findall(inputText)

# Remove duplicates by converting the list into a dictionary, and then back into a list. 
UUIDs = list(dict.fromkeys(UUIDs))

# Create an empty dictionary that will hold the mapping of the old UUIDs to new UUIDs
UUIDDict = {}

# Loop through the list of UUIDs, adding them to the dictionary along with new UUID replacements.
# uuid.uuid4 will be used, this will create psuedo random UUIDs
for item in UUIDs:
    newUUID = str(uuid.uuid4())
    UUIDDict[item] = newUUID

# Main UUID replace loop; Loop through the dictionary, and for each UUID replace it with the new UUID in the inputText
for UUID in UUIDDict:
    replacementUUID = UUIDDict[UUID]
    inputText = inputText.replace(UUID, replacementUUID)

# Replace the realm name and realm display name
inputText = inputText.replace('REALM_NAME', realmName)
inputText = inputText.replace('REALM_DISPLAY_NAME', realmDisplayName)

# Create the new frontend URL string and replace the one in the input text
newURL = str("https://" + realmName + ".piqlconnect.com").lower()
inputText = inputText.replace('FRONTEND_URL', newURL)

# Open the output file, write the data to the file, and close it again
outputFile = open(outputFileName, 'w')
outputFile.write(inputText)
outputFile.close()