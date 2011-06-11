versioncheck.php is the file needed in the root of the freenas domain for checking new firmware updates!
it looks the in 3 seperate txt files for the version numbers of the latest online releases.

- lateststable
- latestnightly
- latestbeta

After a new FreeNAs release the textfile's on the checkversion website needs to be updated!


 

*
It needs to placed here to have the translation strings in the pot file.
the po files (locale folder) are than needed on this server and needs manual stripped by updating the po files with poedit and 
import the versioncheck.php to have left 10 strings only!

Sorry but i did not found a way to get this be done automatic yet with the translation strings on 1 and the same potfile to host.

Michael