@extends('default')

@section('content')
<div class="container">
 <h1>Changelog</h1>
  <p class="intro">
    Going forward we will be adding updates and new features in this changelog. 
    Feel free to request certain features or report certain bugs in the comments.
  </p>

  <div class="changelog-entry">
    <h2>9 April 2025</h2>
    <ul>
      <li><strong>Improvement:</strong> We've made sure that if a note has a password protection set, the content of the URL is no longer visible on the <code>/share/</code> URL and the search results.</li>
    </ul>
  </div>

  <div class="changelog-entry">
    <h2>1 April 2025</h2>
    <ul>
      <li><strong>New feature:</strong> Speech-to-text added in notes. You can now press the mic button and speak to enter text into the note. You can also use words like 'enter', 'full stop', 'question mark', etc. This only works with Chrome and Edge browsers, and only on desktop and Android (iOS does not support this function).</li>
    </ul>
  </div>

  <div class="changelog-entry">
    <h2>24 March 2025</h2>
    <ul>
      <li><strong>New feature:</strong> Added favorite button to notes & shared notes. This allows you to easily access notes that are of interest to you. We've also added a menu item 'Favorite Notes' where you can find all the notes that you've favorited.</li>
      <li><strong>New feature:</strong> Added duplicate button to shared notes, which now allows you to duplicate the content of a shared note to a new note where you can edit the content for yourself.</li>
      <li><strong>Improvement:</strong> You can now sort the search results in ASC or DESC.</li>
      <li><strong>Improvement:</strong> You can now search multiple words; only those notes that contain all words in your search query will be displayed.</li>
      <li><strong>Improvement:</strong> Created separate sitemap files to speed up the loading of the sitemap.</li>
    </ul>
  </div>

  <div class="feature-requests">
    <h2>Feature requests:</h2>
    <ul>
      <li>Export note/download</li>
      <li>Title for note</li>
      <li>UTF-8 support</li>
      <li>Leave a comment</li>
    </ul>
  </div>
</div>
@endsection