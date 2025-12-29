import 'package:isar/isar.dart';

part 'death_event.g.dart';

@Collection()
class DeathEvent {
  Id id = Isar.autoIncrement;

  // Event attributes
  int? createdBy;
  int? confirmedBy;
  int? animalId;
  String? source;
  DateTime? eventDate;
  String? comment;
  bool? isConfirmed;
  String? uid;
  int? version;

  // Death specific
  String? cause;
  String? deathPlace;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();

  DeathEvent({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
    this.cause,
    this.deathPlace,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory DeathEvent.fromJson(Map<String, dynamic> json) {
    return DeathEvent(
      createdBy: json['created_by'] as int?,
      confirmedBy: json['confirmed_by'] as int?,
      animalId: json['animal_id'] as int?,
      source: json['source'] as String?,
      eventDate: json['event_date'] == null
          ? null
          : DateTime.parse(json['event_date'] as String),
      comment: json['comment'] as String?,
      isConfirmed:
          json['is_confirmed'] == null ? null : (json['is_confirmed'] as bool),
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
      cause: json['cause'] as String?,
      deathPlace: json['death_place'] as String?,
      createdAt: json['created_at'] == null
          ? null
          : DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] == null
          ? null
          : DateTime.parse(json['updated_at'] as String),
      deletedAt: json['deleted_at'] == null
          ? null
          : DateTime.parse(json['deleted_at'] as String),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'created_by': createdBy,
        'confirmed_by': confirmedBy,
        'animal_id': animalId,
        'source': source,
        'event_date': eventDate?.toIso8601String(),
        'comment': comment,
        'is_confirmed': isConfirmed,
        'uid': uid,
        'version': version,
        'cause': cause,
        'death_place': deathPlace,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'deleted_at': deletedAt?.toIso8601String(),
      };
}
