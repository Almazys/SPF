#!/bin/python3
import os
import sys
import re

# check if an argument were given 
if len(sys.argv) <2:
	print("You need to specify controller's path/to/controller")
	sys.exit()

destFile = sys.argv[1].split('/')[-1].capitalize()
destFile = re.sub('.class.php', '', destFile)
destFile = re.sub('.php', '', destFile)
if destFile:
	destFile = destFile + ".class.php"
else
	destFile = "Index.class.php"

destPath = os.getcwd() + '/'
if len(sys.argv[1].split('/'))>1:
	destPath = destPath + '/'.join(sys.argv[1].split('/')[:-1]).lower() + '/'

print("[*] Checking folders...")
if not os.path.exists(destPath):
	print(" [~] Creating folders ...")
	os.makedirs(destPath)

print("[*] Checking file...")
if os.path.isfile(destPath + destFile):
	answer = input(" [~] File " + destFile + " already exists. Overwrite ? [Y/N] ")
	if (answer!='Y') & (answer!='y'):
		print("Skipping...")
		sys.exit()

# Writing into new class file
print("[*] Creating class file " + destPath + destFile + '... ', end="")

try:
	with open(destPath + destFile, 'w') as file:
		file.write(
'''<?php

/**
 * Class generated through ''' + __file__ +  '''
 * @author: Almazys
 */

class ''' + destFile.replace(".class.php", "") + ''' extends WebsiteController {

	public function __construct() {
		parent::__construct();
		$this->work();
	}

	public function work() {
		# Do stuff here ...
	}

}

?>
''')
		file.close()

except IOError as e:
	print("failed !")
	print(str(e))
	sys.exit()

print("success !")
