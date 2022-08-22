<?php
class Home{

	public static function page(){
		$ballot = new Table("ballot");?>
<h3>Preliminary points:</h3>
<p>College guarantees to offer a room for every undergraduate for the
	duration of their course. The room/housing ballot allows students to
	have a say in the room they are allocated. Students that do not enter
	the ballot must either sort out their own accommodation or will be
	allocated a random room by college (after the balloting process is
	finished).</p>
<p>The housing/room ballot system has been heavily updated, please refer
	to the below video for guidance on how to use the system itself. For
	details of the entire ballot process, please refer to the remaining
	details on this page. Furthermore, the system is live, details of rooms
	such as their photos, rents, allocations and specific dates, etc. etc.
	are likely to change throughout the balloting period, so please ensure
	to keep checking this website for updates or changes.</p>
<p>Key points of contact:</p>
<p>
<ul>
	<li>JCR Accommodation and Services Officer – Ishaka De Bessou:
		jcr.services@fitz.cam.ac.uk</li>
	<li>JCR Website and Technology Officer – Kyle Van Der Spuy:
		jcr.website@fitz.cam.ac.uk</li>
</ul>
</p>
<video width="640" height="340" controls>
	<source src="include/Ballot_images/Videos/system_walkthrough.mp4"
		type="video/mp4">
</video>
<h3>Ballot timetable:</h3>
<table class="table table-condensed table-bordered table-hover">
	<thead>
		<tr>
			<td>Event</td>
			<td>Date</td>
		</tr>
	</thead>
	<tr>
		<td>Registration for Housing and Room Ballot Opens</td>
		<td><?= $ballot->get("reg_open") ?></td>
	</tr>
	<tr>
		<td>Registration for Housing Ballot Closes and Housing Ballot is Drawn</td>
		<td><?= $ballot->get("hb_drawn") ?></td>
	</tr>
	<tr>
		<td>Housing Allocation finishes by</td>
		<td><?= $ballot->get("hb_dead") ?></td>
	</tr>
	<tr>
		<td>Registration for Room Ballot Closes and Room Ballot is Drawn</td>
		<td><?= $ballot->get("rb_drawn") ?></td>
	</tr>
	<tr>
		<td>Room Allocation finishes by</td>
		<td><?=$ballot->get("rb_dead")?></td>
	</tr>
	<tr>
		<td>Specific Room allocation and Contract sgning deadline (for houses and rooms)</td>
		<td><?=$ballot->get("contract_dead") ?></td>
	</tr>
</table>
<h3>Registration - <?=$ballot->get("reg_open") ?>:</h3>
<p>
<ul>
	<li>To register for either ballot, students must go onto the
		Registration page and provide all details as requested by the system.</li>
	<li>The housing ballot is only available for first years.</li>
	<li>You can still register for the room ballot while the housing ballot
		is being run.</li>
	<li>Students with access arrangements or a disability giving rise to
		specific accommodation needs should contact their tutor, or the senior
		tutor, to discuss their specific requirements. If you are offered and
		agree to take a specific room on this basis, that room will then be
		withdrawn from the ballot, and you do not then need to register and
		take part in the ballot.</li>
	<li>Access arrangements are provided to students on an individual
		basis, not to those wishing to ballot as part of a larger group.</li>
	<li>If you wish to withdraw from either ballot, email
		jcr.services@fitz.cam.ac.uk who will remove you from the ballot as
		requested. If you then wish to rent privately, you need to state this,
		so a room is not randomly allocated for you once the balloting process
		is finished.</li>
</ul>
</p>
<h3>Housing Ballot - <?=$ballot->get("hb_dead") ?> deadline:</h3>
<p>
<ul>
	<li>After registering, you can form your groups on the Group Editor
		page (see the above video for details on how it works).</li>
	<li>Houses will be allocated on the basis of group size, not by
		particular house.</li>
	<li>There are only houses available for groups of 4 to 9.</li>
	<li>The group admin (who created the group or was handed down admin
		status) will be responsible for going onto the system and choosing the
		house for the group when it is their turn.</li>
	<li>While everyone can still nominate a proxy, only the group
		admin{ap}s proxy will be given access to choose a house for the group,
		if the group admin is unable to. Your proxy can be someone else within
		your group.</li>
	<li>The role of allocating specific rooms within the house to specific
		members in the group is also given to the group admin. It is
		encouraged that this is an open decision within your group and
		priority is given to any member of the group with specific
		requirements (to students that have permission and require a piano in
		their room, please ensure that you choose a suitable room; these
		should be on the ground floor and a L (Large) size in order to fit),
		and then to students who have previously lived in non-refurbished
		rooms in their first year. If you have any other specific requirement,
		please ensure that you choose a suitably sized room to accommodate it.</li>
	<li>Any group that is not allocated a house will automatically be
		registered to the room ballot once the housing ballot is completely
		finished. Group members will be registered individually (see below)
		but will be on equal footing with those who registered directly for
		the housing ballot.</li>
	<li>The room ballot cannot start until all houses are allocated to
		groups.</li>
	<li>Please see the Houses page (under the Housing Ballot tab) for
		details of all available houses and their rooms, this will
		automatically update as houses are chosen.</li>
</ul>
</p>
<h3>Room Ballot - <?=$ballot->get("rb_dead") ?> deadline:</h3>
<p>
<ul>
	<li>The room ballot cannot start until all houses are allocated to
		groups via the housing ballot.</li>
	<li>Students who have automatically been transferred to the room
		ballot, from the housing ballot, will be added individually.
		Therefore, they will need to remake their groups again for the room
		ballot.</li>
	<li>After registering, you can form your groups on the Group Editor
		page (see the above video for details on how it works).</li>
	<li>Priority in the ballot is given in the following order: Second
		years and Third years abroad, Third years with confirmed fourth year,
		First years. The category {ap}Third years on specific courses with
		confirmed fourth year{ap} excludes students planning to study for
		integrated Masters courses in their fourth year, and those hoping to
		study Management Studies as an additional year; these students will be
		offered rooms in summer when 4th year numbers are confirmed following
		Tripos results.</li>
	<li>If balloting in a group, the group{ap}s overall priority is
		determined by the lowest individual member{ap}s priority (according to
		their year group) within the group.</li>
	<li>If balloting in a group, the group admin (who created the group or
		was handed down admin status) will be responsible for going onto the
		system and choosing rooms for the group when it is their turn.</li>
	<li>Again, if balloting in a group, while everyone can still nominate a
		proxy, only the group admin{ap}s proxy will be given access to choose
		rooms for the group, if the group admin is unable to. Your proxy can
		be someone else within your group.</li>
	<li>The maximum group size is {GroupLimit}.</li>
	<li>Balloting groups do not have to select rooms in a single corridor
		or block, you just have the same position in the ballot. You can split
		your group into smaller groups when picking rooms if you want to via
		room allocation.</li>
	<li>To students that have permission and require a piano in their room,
		please ensure that you choose a room on the ground floor and a large
		size in order to fit.</li>
	<li>Please see the Rooms page (under the Room Ballot tab) for details
		of all available rooms, this will automatically update as rooms are
		chosen.</li>
</ul>
</p>
<h3>Drawing either Ballot - <?= $ballot->get("hb_drawn") ?> and <?= $ballot->get("rb_drawn") ?>:</h3>
<p>
<ul>
	<li>The ballot will be drawn live in the JCR.</li>
	<li>First a seed will be drawn and made visible, if there is any doubt
		that the seed is not random enough, this must be stated, and if
		reasonable, the seed will be redrawn.
	<li>The seed is a big number that drives the randomness of the random
		number generator (PHP{ap}s Mersenne Twister) used within the
		programme.</li>
	<li>The programme works by picking balloting groups one at a time from
		the overall set, like picking names from a hat (the Fisher-Yates
		shuffle).</li>
	<li>Room or house allocation will not start immediately, the ballot
		will still be checked to see if everyone has been included and changes
		will be made where appropriate (e.g. for the JCR president(s) to be
		given first pick).</li>
	<li>The code for the ballot is publicly available at insert github
		link, if you would like to make changes, please contact
		jcr.website@fitz.cam.ac.uk.</li>
</ul>
</p>
<h3>Allocating Rooms - <?=$ballot->get("contract_dead") ?> deadline:</h3>
<p>
<ul>
	<li>Rooms/houses are chosen online at the Room Allocator page,
		following the order of the ballot drawn.</li>
	<li>If you have a specific requirement, please ensure that you choose a
		suitably sized room to accommodate.</li>
	<li>A group only needs to allocate itself as many rooms as there are
		members in the group for the ballot to move on to the next group in
		the ballot.</li>
	<li>Individual members of a group can be assigned individual rooms
		later, but before the final deadline {RoomDeadline}.</li>
	<li>Once it is a student{ap}s turn to allocate themselves or their
		group rooms, they will be notified by email. If they take longer than
		a day, a second email will be sent to prompt them it is their turn.
		Furthermore, access will then be opened up to their proxy in addition
		to the group admin to allocate rooms for them or their group.</li>
	<li>If they take longer than two days, the group will be pushed to the
		bottom of the ballot, regardless of their priority (e.g. a group of
		second years will be pushed after the first years, not at the end of
		the remaining second years), this will allow the ballot to continue.</li>
	<li>After a group has been pushed to the end of the ballot, the group
		admin will still be able to allocate themselves rooms by emailing
		jcr.website@fitz.cam.ac.uk, however, they will only then be able to
		select from those available at that given time.</li>
	<li>If the group admin does not have a proxy, the rest of the group
		will not be checked for a proxy before continuing.</li>
	<li>If students run into difficulties and want to swap/change rooms for
		whatever reason, contact jcr.services@fitz.cam.ac.uk. To do this, the
		entire balloting system may be put into maintenance mode, preventing
		everyone from accessing the system so please do not panic if this
		happens.</li>
</ul>
</p>
<h3>Signing Rent Contracts - <?=$ballot->get("contract_dead") ?> deadline:</h3>
<p>
<ul>
	<li>Once rooms have been allocated to specific students, you will be
		asked to provisionally select the type of (rent) contract you want by
		a form sent via email.</li>
	<li>There are three contracts:</li>
	<li>A – Termly residence (29 weeks), covers residency of michaelmas,
		lent and easter only.</li>
	<li>B – Easter vacation residence (29 weeks + easter vacation), covers
		residency of michaelmas and the period from the beginning of lent{ap}s
		residency period to the end of easter{ap}s residency period.</li>
	<li>C – Full residence (29 weeks + easter and Christmas vacation),
		covers period from the beginning of michaelmas{ap} residency period to
		the end of easter{ap}s residency period.</li>
	<li>All contracts include: fully furnished room (with bed, desk, chair
		and storage space), ensuite or shared toilet facility, use of gyp or
		kitchen space, heating, power and lighting (to agreed college
		standards), daily cleaning of common areas and weekly cleaning of
		bedrooms, IT services via the college network and use of the shared
		laundry facilities.</li>
	<li>If you wish to stay outside your contract, you will be able to
		request out of term time accommodation. The college will try to find a
		room for you, but cannot guarantee it will be you term-time room.</li>
</ul>
</p>
<h3>Further Remarks</h3>
<p>
<ul>
	<li>There will be no shadow ballot, as the ballot is now automatic,
		students have more time to discuss the rooms they want within their
		groups and make a decision. The system is also a lot more flexible, so
		if changes to room allocations still want to be made before the ballot
		closes (maintaining the order of the ballot as much as possible) they
		can be.</li>
	<li>Usually, an open weekend is held before the ballot system becomes
		live, however, due to covid this again has not been a possibility this
		year. However, a list of house and corridor reps will be sent out by
		the accommodation and services officer soon, whom you can ask for
		details and more information about the houses/rooms being balloted
		for.</li>
	<li>For security reasons, we request that you keep information
		regarding room rents and photographs of College-owned rooms strictly
		confidential to the members of Fitzwilliam College. You are also
		warned that exploiting any online vulnerabilities in Ballot System
		will not be tolerated. If anyone is known to have violated these
		rules, all information will be withdrawn and the person in question
		will be referred to the Senior Tutor and the Dean.</li>

</ul>
</p>
<h3>About this site</h3>
<p>This system was originally built by Charlie Jonas (webmaster 2016),
	Tom Benn (webmaster 2017) and updated by Daniel Carter (webmaster 2018
	+ 2019). It has undertaken a major update by Kyle Van Der Spuy (website
	and technology officer 2021), however, the infrastructure of the system
	is the same. If you have any problem with the housing ballot you should
	contact the Accomodation and Services Officer at
	jcr.services@fitz.cam.ac.uk. To report any technical issues with this
	website you are invited to contact the JCR Website and Technology
	Officer in confidence at jcr.website@fitz.cam.ac.uk.</p>
<?
	}
}