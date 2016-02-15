Do-Want
=======

Do Want is an Open Source Gift Registry system designed to be quick and easy to setup and run on your preferred web
server. 

Whether you're exchanging gifts with just one other person or with 50 other people, Do Want makes it easy to pick out 
gifts and make sure no one else ends up with the same thing.

Looking for a Download?
=======================
If you just want to download an installable, reasonably stable version, you can visit the [end-user-friendly page here](http://aaroneiche.github.com/do-want/).

Installation
============
####The preferred way

Download the most recent version from the link above and go to the directory where you placed it in your browser. You will be redirected to the installation page.

####The alternative way
If you would prefer to use the most recent code (at your own risk), or at least the most recent code committed, you can download the code on the github (https://github.com/aaroneiche/do-want) This version is not guaranteed to work properly, though the code should be relatively complete.

You will need to have [Composer installed](https://getcomposer.org/), Then do the following:

```
composer install
```

That will make composer fetch all the appropriate files 
Additionally, you will need to install a customized version of the Phinx TextWrapper Class [found here](https://github.com/aaroneiche/phinx/tree/0.5.x-dev/src/Phinx/Wrapper) - this version supports seed commands from php applications. Simply replace the corrosponding file in your vendor/robmorgan/phinx/src/Wrapper/ with the new one. A pull request is into the Phinx project to get the file updated.


Migrating from a previous version
=================================
There are two steps necessary to migrate from a previous installation of DoWant. This is a temporary solution while a more elegant process is put into place. 

1) Copy all new files from the distribution to your installation. Many FTP programs have a sync feature that will accomplish this.

2) Update your database; In your browser, go to your wishlist and to /migrator.php  Here you will find a table of migrations - simply click on the last available one. This will migrate your installation to the most recent database version. After that, you're done!


Contributing
============
If you're willing to contribute, I'm delighted to have you work on it! The workflow is pretty simple:
	
	1) Fork the "develop" branch (it's the default)
	2) Create a branch to fix an issue, or implement a feature
	3) Write code (pull 'develop' and merge it into your branch as you go to stay up to date)
	4) Push that branch to your Github
	5) Issue a pull request to the develop branch

I'm still figuring out unit testing, and the release cycle. I figure once we tackle a few bugs, we'll merge into master and 
tag a release.

Feel free to email with questions, comments, concerns, or ideas: aaron@aaroneiche.com