<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dct="http://dublincore.org/documents/2012/06/14/dcmi-terms/" version="2.0">
  <channel>
    <title>Realfagsbiblioteket</title>
    <description>Videoer om vitenskap</description>
    <atom:link rel="self" type="application/rss+xml" href="https://blekkio.uio.no/feed" />

    @foreach ($events as $gid => $event)
    <item>
      <id>{{ $gid }}</id>
      <pubDate>{{ $event['vortexEvent']->start_time }}</pubDate>
      <title>{{ $event['title'] }}</title>
      @if (isset($event['playlist']))
      <media:embed url="{{ $event['playlist']->youtubeLink('embed') }}" />
      @else
      <media:embed url="{{ $event['recordings'][0]->youtubeLink('embed') }}" />
      @endif
      <media:content url="{{ array_get($event['recordings'][0]->youtube_meta, 'thumbnail') }}" />
      <description><![CDATA[
        {!! $event['recordings'][0]->youtubeDescriptionAsHtml() !!}
      ]]></description>
    </item>
    @endforeach
  </channel>
</rss>
