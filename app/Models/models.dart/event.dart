import 'package:isar/isar.dart';
import 'health_event.dart';
import 'movement_event.dart';
import 'transaction_event.dart';
import 'reproduction_event.dart';
import 'birth_event.dart';
import 'milk_record.dart';
import 'death_event.dart';
import 'weight_record.dart';
import 'evidence_file.dart';

part 'event.g.dart';

@Collection()
class Event {
  Id id = Isar.autoIncrement;

  int? createdBy;
  int? confirmedBy;
  int? animalId;
  String? source;
  DateTime? eventDate;
  String? comment;
  bool? isConfirmed;
  String? uid;
  int? version;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();

  final healthEvent = IsarLink<HealthEvent>();
  final movementEvent = IsarLink<MovementEvent>();
  final transactionEvent = IsarLink<TransactionEvent>();
  final reproductionEvent = IsarLink<ReproductionEvent>();
  final birthEvent = IsarLink<BirthEvent>();
  final milkRecord = IsarLink<MilkRecord>();
  final deathEvent = IsarLink<DeathEvent>();
  final weightRecord = IsarLink<WeightRecord>();

  final evidenceFiles = IsarLinks<EvidenceFile>();

  Event({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
  });

  factory Event.fromJson(Map<String, dynamic> json) {
    return Event(
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
    );
  }

  Map<String, dynamic> toJson() {
    return {
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
    };
  }
}
