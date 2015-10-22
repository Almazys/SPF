#!/bin/python3
import os
import sys
import re

# check if an argument were given 
if len(sys.argv) < 2:
	print('Usage: ' + sys.argv[0] + ' path/to/controller [author]')
	sys.exit()

if len(sys.argv) == 3:
	author = sys.argv[2]
else:
	author = ""

destFile = sys.argv[1].split('/')[-1].capitalize()
destFile = re.sub('.class.php', '', destFile)
destFile = re.sub('.php', '', destFile)
if destFile:
	destFile = destFile + ".class.php"
else:
	destFile = "Index.class.php"

destPath = os.getcwd() + '/'
if len(sys.argv[1].split('/'))>1:
	destPath = destPath + '/'.join(sys.argv[1].split('/')[:-1]).lower() + '/'

print('[*] Checking folders...')
if not os.path.exists(destPath):
	print(' [~] Creating folders ...')
	os.makedirs(destPath)

print('[*] Checking file...')
if os.path.isfile(destPath + destFile):
	answer = input(' [?] File ' + destFile + ' already exists. Overwrite ? [Y/N] ')
	if (answer!='Y') & (answer!='y'):
		print('Skipping...')
		sys.exit()

answer = input('[?] Do you want your controller to extend from a "Master Controller" ? [Y/N] ')
if (answer!='Y') & (answer!='y'):
	parent="CoreController"
else:
	parent="WebsiteController"

if (not os.path.isfile(os.getcwd() + '/../application/WebsiteController.class.php')) & (parent == "WebsiteController"):
	# Writing WebsiteController class file
	print('[*] Creating class file ' + os.getcwd() + '/../application/WebsiteController.class.php' + '... ', end='')

	try:
		with open(os.getcwd() + '/../application/WebsiteController.class.php', 'w') as file:
			file.write(
	'''<?php

abstract class WebsiteController extends CoreController {

	public function __construct() {
		parent::__construct();
		Debug::write("Building common WebsiteController ...", 0);
		
		/**
		 * Do common stuff concerning your website here
		 */
	}

}

?>''')

	except IOError as e:
		print("failed to write file !")
		print(str(e))
		sys.exit()

	print("success !")



# Writing into new class file
print('[*] Creating class file ' + destPath + destFile + '... ', end='')

try:
	with open(destPath + destFile, 'w') as file:
		file.write(
'''<?php

/**
 * Class generated through create.py
 * @author: ''' + author + '''
 */

class ''' + destFile.replace(".class.php", "") + ''' extends ''' + parent + ''' {

	public function __construct() {
		parent::__construct();
		$this->work();
	}

	public function work() {
		# Do stuff here ...
		# $this->view->setTemplate("specific_template");
		# $this->view->setLocale("en");
	}

}

?>
''')

except IOError as e:
	print("failed to write file !")
	print(str(e))
	sys.exit()

print("success !")
