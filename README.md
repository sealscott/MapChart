# **MapChat**
MapChat is a social media network that utilizes a map to view posts and weather information.

## **Purpose**
This application is aimed to act as an alternative to other social medias. It also serves as a weather application that shows current weather information for any location, and defaults to either the user's current location or will default in Morgantown, WV. 
The application is simplistic, and is an easy way to share pictures with others.

## **Contributions**
Outside API's used for assistance with this app:
* [Leaflet API](https://leafletjs.com/reference.html): Map reference
* [OpenStreetMap](https://osmfoundation.org/wiki/Main_Page): Map tiles

Beyond the above APIs, all code was written by WVU students taking CS330. Contributors are listed below:
* [Jett Nicolette](https://github.com/jettnicolette)
* [Seth Scott](https://github.com/sealscott)
* [Mason Williams](https://github.com/MWilly26)
* [Jacob Wolfe](https://github.com/Jvvol)
* [David Kotlowski](https://github.com/DavidKot4)
* [Noah Gorospe](https://github.com/noahgorospe1)

## **Languages**
The application is written mostly using PHP 8.4, with some JavaScript for the Leaflet Map and geolocation services.

## **User Actions**
When designing the application, we wanted to ensure that users can have a personalized experience in the app, and provide the ability to interact with other users as easily as possible

### **Posting**
Users can post images with captions to the app, and will display on the map when users have location services on. Users with location services off will not have their posts displayed on the map. Posts older than 24 hours will not be able to be viewed by other users, but
will be viewable to the poster on their own profile page. To provide security to users, ensuring that posts do not appear at their precise location, post latitude and longitude is only specific to the fourth decimal point, allowing for a more generalized area of where
posts were made. Posts are also checked against a content filter to ensure that bad-language is not used.

### **Liking and Commenting**
All posts can be liked and commented on by any user. Posts can be both liked and unliked, but currently users can only comment on posts, without being able to delete their comment.

### **Customizing Profile**
Users are able to personalize their own profile at any time, with the ability to change their profile picture, bio and display name. 

### **Friends**
Users are able to add and remove friends from the other person's profile page.

### **Direct Messaging**
Users are able to direct message other users that they are friends with. Currently, messages are only able to be viewed if you are currently friends with that person. Unfriending someone will remove the ability to see your direct messages, but direct messages will still
be there if you readd the user as a friend.

## **Feed Algorithm**
In its current state, the feed algorithm puts priority on three variables in this order: 
1. [Friends](#friends-1)
2. [Location](#location)
3. [Time](#time)

When put together, the algorithm provides a personalized experience for the user, allowing them to control what posts they see first.

### Friends
As a part of the customized experience for users, we want to ensure that posts by friends appear at the top of their feed page. By adding other users as friends, we assume that users want to see what they have posted before anything else. Due to that, we have given
friends the highest priority when viewing your feed. 

### Location
Next, due to the purpose of the app, filtering by location is an important aspect of the application. Depending on the user's current location, a radius will be created to collect all posts in that radius, then will increase by a set increment. This continues until
either there are no posts left to sort, or until the entire globe is covered. Posts without a location are pushed to the bottom of the priority.

### Time
Finally, posts are sorted by their post-time. Newer posts take priority over older posts, allowing users to get the most recent information from other users.

## Security
Due to the application holding sensitive user information, security has been important throughout the development process. To ensure that user information is secure, all calls to our database have been made to prevent SQL injections as much as possible. User passwords
are hashed to ensure that they are not held in plain-text.

## Future Work
With continuing development on the application, there are many more additions we are working on:
* UI Finalization
* Private Accounts
* Message Encryption
* Admin Accounts
* Bot/Spam Removal
* Comment Deletion
* More Accessibility Settings
* Caption Auto-Generator
