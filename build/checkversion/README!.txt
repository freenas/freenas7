About this files:
-----------------

versioncheck.php is the file needed in the root of the freenas domain for checking on new firmware updates!
It does lookup there in 3 seperate .txt files for the version numbers of the latest online releases.

- lateststable.txt
- latestnightly.txt
- latestbeta.txt

After a new FreeNAS release the textfile's on the checkversion website needs to be updated!


Info about the po files:
------------------------

The .po files from svn/locale folder are than needed on this firmwarecheck server and needs manual updated with the freenascheckversion.pot file.
Rename the potfiles in its locale directory to messages.po to make it works.
I found no otherway (yet) to get this translations done on our same  https://translations.launchpad.net/freenas/trunk/+pots/freenas website.

Michael

end!