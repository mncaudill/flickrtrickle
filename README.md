# flickrtrickle

## Who?
Nolan Caudill <nolan@nolancaudill.com>

## What?
FlickrTrickle lets you slowly introduce photos you upload into Flickr into your contact's streams so they'll get seen by your contacts and to work around Flickr's interface that only shows the last five photos show from a contact, regardless of how many they just uploaded.

This is the code behind <a href="https://flickrtrickle.com">FlickrTrickle</a>.

## Why?
So let's say you go on a trip, like hiking the Appalachians, touring federal prisons, or visiting Disneyland, and get shutter-happy and take more than 5 photos. With the rise of digital cameras, taking multiple pictures in one day is not unheard of, and is practically encouraged in some circles. Now, you want to upload your more-than-a-handful number of pictures to Flickr. You're quite proud of these photos, either due to their composition, their dynamics, or the way the light plays softly against edges of your latte art next to the kitten, and want your friends to see them. But Flickr (rightfully) wants to keep your photos-from-your-friend's stream from being dominated by one person if they were to upload 150 photos so they only show the last five uploaded from each your contacts. This is very egalitarian of them.

Through anecdotal experience, anything beyond that 5-photo barrier gets next to no views. Maybe my storytelling abilities don't drive people to want to see those next photos but the interface doesn't help me out either. 

You want more. You want your friends to see your photos. The best way to make this happen for me is to only upload a maximum of five at a time. Remembering to do this and keeping track of what you've already made public is an exercise in bookkeeping, something that computers are quite good at and my wife will attest to that I'm terrible at.

## How?

The way around this is to use three different Flickr features: tags, privacy settings, and the date-posted-at attribute.

Flickr sorts your photos in the global stream by the date posted to Flickr. Like most things on the site, this is adjustable by the API, meaning you can lie to the computers and say "yep, I uploaded this one a second ago even though you saw me upload it two weeks ago." Computers are gullible that way. We can use that superpower and upload photos to Flickr whenever we'd like but set them private just to get them up there. Sometimes you want a photo to be a private without being visible to this trickling-interface (trickle-face?) so the code only pulls photos you've tagged with "flickrtrickle." 

Then through a magical web interface (aka, this code), you select the ones you want to be visible to your friends and the date stuff automatically work itself out so it looks like you uploaded them at that moment.

You get to upload all your photos to Flickr at one time, and then "trickle" them in a few at a time. 

## Installation

This is a PHP app so a server environment that can execute that is required. I use Apache. This directory makes use of .htaccess files so Apache is probably required to use this codebase out of the box.

The ```site``` directory in this repo is the top-level directory that gets served. Point your docroot at this. There are .htaccess rules included that keep the lib and config files private, so make sure you set up Apache to read these.

You'll need to copy the ```include/config.php.sample``` to ```prod_config.php``` with your Flickr key and secret included. Inside of ```init.php``` file you'll see a little bit of code that you can edit to load different configs per server environment, if you so desire.

There's no database required for this as the only state the app maintains is see who is logged-in and store their Flickr token in the app.
